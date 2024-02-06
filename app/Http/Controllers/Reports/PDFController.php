<?php

namespace App\Http\Controllers\Reports;

use App\Models\Shift;
use App\Models\Device;
use App\Models\Company;
use App\Models\Employee;
use App\Models\ShiftType;
use App\Models\Attendance;
use App\Models\Department;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use Illuminate\Database\Eloquent\Builder;

class PDFController extends Controller
{

    public function daily_summary(Request $request)
    {
        return Pdf::loadView('pdf.html.daily.daily_summary')->stream();
    }
    public function weekly_summary(Request $request)
    {
        return Pdf::loadView('pdf.html.weekly.weekly_summary_v1')->stream();
    }
    public function monthly_summary()
    {
        return Pdf::loadView('pdf.html.monthly.monthly_summary_v1')->stream();
    }

    public function dailyAccessControl()
    {
        return Pdf::loadView('pdf.html.daily.access_control')->stream();
    }
    public function weeklyAccessControl()
    {
        return Pdf::loadView('pdf.html.weekly.access_control')->stream();
    }
    public function monthlyAccessControl()
    {
        return Pdf::loadView('pdf.html.monthly.access_control')->stream();
    }
    public function monthlyAccessControlV1()
    {
        return Pdf::loadView('pdf.html.monthly.access_control_v1')->stream();
    }
    public function monthlyAccessControlCount()
    {
        return Pdf::loadView('pdf.html.monthly.access_control_count')->stream();
    }
    public function monthlyAccessControlByDevice()
    {
        return Pdf::loadView('pdf.html.monthly.access_control_by_device')->stream();
    }

    public function testPDF()
    {
        $dataArray = [];

        // Populate the array with dummy data for demonstration purposes
        for ($i = 0; $i < 30; $i++) {
            $dataArray[] = [
                'id' => $i + 1,
                'name' => 'John Doe',
                'phone' => '123-456-7890',
                'code' => '101',
                'date' => '2024-01-25 08:00:00',
                'startTime' => '08:00 AM',
                'endTime' => '05:00 PM',
                'mode' => 'Entry',
                'status' => 'Present',
                'user_type' => 'Employee',
            ];
        }

        $chunks = array_chunk($dataArray, 20);
        return Pdf::setPaper('a4', 'landscape')->loadView('pdf.access_control_reports.report', ["chunks" => $chunks])->stream();
    }

    public function accessControlReportPrint(AttendanceLog $model, Request $request)
    {
        $model = AttendanceLog::query();

        $model->where("company_id", $request->company_id);

        $model->whereDate('LogTime', '>=', $request->filled("from_date") && $request->from_date !== 'null' ? $request->from_date : date("Y-m-d"));

        $model->whereDate('LogTime', '<=', $request->filled("to_date") && $request->to_date !== 'null' ? $request->to_date : date("Y-m-d"));

        $model->whereHas('device', fn ($q) => $q->whereIn('device_type', ["all", "Access Control"]));

        $model->whereHas('employee', fn ($q) => $q->where("company_id", $request->company_id));

        $model->when(request()->filled("UserID"), function ($query) use ($request) {
            return $query->where('UserID', $request->UserID);
        });

        $model->when(request()->filled("DeviceID"), function ($query) use ($request) {
            return $query->where('DeviceID', $request->DeviceID);
        });


        $model->when($request->filled('device'), function ($q) use ($request) {
            $q->where('DeviceID', $request->device);
        })
            ->when($request->filled('system_user_id'), function ($q) use ($request) {
                $q->where('UserID', $request->system_user_id);
            })
            ->when($request->filled('devicelocation'), function ($q) use ($request) {
                if ($request->devicelocation != 'All Locations') {

                    $q->whereHas('device', fn (Builder $query) => $query->where('location', 'ILIKE', "$request->devicelocation%"));
                }
            })
            ->when($request->filled('employee_first_name'), function ($q) use ($request) {
                $key = strtolower($request->employee_first_name);
                $q->whereHas('employee', fn (Builder $query) => $query->where('first_name', 'ILIKE', "$key%"));
            })
            ->when($request->filled('branch_id'), function ($q) {
                $q->whereHas('employee', fn (Builder $query) => $query->where('branch_id', request("branch_id")));
            })

            ->when(
                $request->filled('sortBy'),
                function ($q) use ($request) {
                    $sortDesc = $request->input('sortDesc');
                    if (strpos($request->sortBy, '.')) {
                        if ($request->sortBy == 'employee.first_name') {
                            $q->orderBy(Employee::select("first_name")->where("company_id", $request->company_id)->whereColumn("employees.system_user_id", "attendance_logs.UserID"), $sortDesc == 'true' ? 'desc' : 'asc');
                        } else if ($request->sortBy == 'device.name') {
                            $q->orderBy(Device::select("name")->where("company_id", $request->company_id)->whereColumn("devices.device_id", "attendance_logs.DeviceID"), $sortDesc == 'true' ? 'desc' : 'asc');
                        } else if ($request->sortBy == 'device.location') {
                            $q->orderBy(Device::select("location")->where("company_id", $request->company_id)->whereColumn("devices.device_id", "attendance_logs.DeviceID"), $sortDesc == 'true' ? 'desc' : 'asc');
                        }
                    } else {
                        $q->orderBy($request->sortBy . "", $sortDesc == 'true' ? 'desc' : 'asc'); {
                        }
                    }
                }
            );

        $model->with('employee', function ($q) use ($request) {
            $q->where('company_id', $request->company_id);
            $q->withOut(["schedule",  "sub_department", "designation", "user"]);

            $q->select(
                "first_name",
                "last_name",
                "profile_picture",
                "employee_id",
                "branch_id",
                "system_user_id",
                "display_name",
                "timezone_id",
                "phone_number",
                "department_id"
            );
        });

        $model->with('device', function ($q) use ($request) {
            $q->where('company_id', $request->company_id);
        });
        if (!$request->sortBy) {
            $model->orderBy('LogTime', 'DESC');
        }


        $data = $model->get()->toArray();

        if ($request->debug) return $data;

        $chunks = array_chunk($data, 10);

        return Pdf::setPaper('a4', 'landscape')->loadView('pdf.access_control_reports.report', [
            "chunks" => $chunks,
            "company" => Company::whereId(request("company_id") ?? 0)->first(),
            "params" => $request->all(),

        ])->stream();
    }

    public function accessControlReportDownload(AttendanceLog $model, Request $request)
    {
        $model = AttendanceLog::query();

        $model->where("company_id", $request->company_id);

        $model->whereDate('LogTime', '>=', $request->filled("from_date") && $request->from_date !== 'null' ? $request->from_date : date("Y-m-d"));

        $model->whereDate('LogTime', '<=', $request->filled("to_date") && $request->to_date !== 'null' ? $request->to_date : date("Y-m-d"));

        $model->whereHas('device', fn ($q) => $q->whereIn('device_type', ["all", "Access Control"]));

        $model->whereHas('employee', fn ($q) => $q->where("company_id", $request->company_id));

        $model->when(request()->filled("UserID"), function ($query) use ($request) {
            return $query->where('UserID', $request->UserID);
        });

        $model->when(request()->filled("DeviceID"), function ($query) use ($request) {
            return $query->where('DeviceID', $request->DeviceID);
        });


        $model->when($request->filled('device'), function ($q) use ($request) {
            $q->where('DeviceID', $request->device);
        })
            ->when($request->filled('system_user_id'), function ($q) use ($request) {
                $q->where('UserID', $request->system_user_id);
            })
            ->when($request->filled('devicelocation'), function ($q) use ($request) {
                if ($request->devicelocation != 'All Locations') {

                    $q->whereHas('device', fn (Builder $query) => $query->where('location', 'ILIKE', "$request->devicelocation%"));
                }
            })
            ->when($request->filled('employee_first_name'), function ($q) use ($request) {
                $key = strtolower($request->employee_first_name);
                $q->whereHas('employee', fn (Builder $query) => $query->where('first_name', 'ILIKE', "$key%"));
            })
            ->when($request->filled('branch_id'), function ($q) {
                $q->whereHas('employee', fn (Builder $query) => $query->where('branch_id', request("branch_id")));
            })

            ->when(
                $request->filled('sortBy'),
                function ($q) use ($request) {
                    $sortDesc = $request->input('sortDesc');
                    if (strpos($request->sortBy, '.')) {
                        if ($request->sortBy == 'employee.first_name') {
                            $q->orderBy(Employee::select("first_name")->where("company_id", $request->company_id)->whereColumn("employees.system_user_id", "attendance_logs.UserID"), $sortDesc == 'true' ? 'desc' : 'asc');
                        } else if ($request->sortBy == 'device.name') {
                            $q->orderBy(Device::select("name")->where("company_id", $request->company_id)->whereColumn("devices.device_id", "attendance_logs.DeviceID"), $sortDesc == 'true' ? 'desc' : 'asc');
                        } else if ($request->sortBy == 'device.location') {
                            $q->orderBy(Device::select("location")->where("company_id", $request->company_id)->whereColumn("devices.device_id", "attendance_logs.DeviceID"), $sortDesc == 'true' ? 'desc' : 'asc');
                        }
                    } else {
                        $q->orderBy($request->sortBy . "", $sortDesc == 'true' ? 'desc' : 'asc'); {
                        }
                    }
                }
            );

        $model->with('employee', function ($q) use ($request) {
            $q->where('company_id', $request->company_id);
            $q->withOut(["schedule",  "sub_department", "designation", "user"]);

            $q->select(
                "first_name",
                "last_name",
                "profile_picture",
                "employee_id",
                "branch_id",
                "system_user_id",
                "display_name",
                "timezone_id",
                "phone_number",
                "department_id"
            );
        });

        $model->with('device', function ($q) use ($request) {
            $q->where('company_id', $request->company_id);
        });
        if (!$request->sortBy) {
            $model->orderBy('LogTime', 'DESC');
        }


        $data = $model->get()->toArray();

        if ($request->debug) return $data;

        $chunks = array_chunk($data, 10);

        return Pdf::setPaper('a4', 'landscape')->loadView('pdf.access_control_reports.report', [
            "chunks" => $chunks,
            "company" => Company::whereId(request("company_id") ?? 0)->first(),
            "params" => $request->all(),

        ])->download();
    }


}
