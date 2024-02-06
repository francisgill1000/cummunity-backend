<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tanent\StoreRequest;
use App\Http\Requests\Tanent\UpdateRequest;
use App\Models\Company;
use App\Models\Tanent;

class TanentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Tanent::where("company_id", request("company_id"))->with(["members", "floor", "room"])->paginate(request("per_page") ?? 10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function validateTanent(StoreRequest $request)
    {
        try {
            $exists = Tanent::where("company_id", $request->company_id)->where('phone_number', $request->phone_number)->exists();

            // Check if the Tanent number already exists
            if ($exists) {
                return $this->response('Tanent already exists.', null, true);
            }

            return $this->response('Tanent Successfully created.', $request->validated(), true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }




    public function store(StoreRequest $request)
    {

        try {
            $exists = Tanent::where("company_id", $request->company_id)->where('phone_number', $request->phone_number)->exists();

            // Check if the Tanent number already exists
            if ($exists) {
                return $this->response('Tanent already exists.', null, true);
            }

            $data = $request->validated();

            $data["full_name"] = "{$data["first_name"]} {$data["last_name"]}";

            // $communityId = $request->floor_id ?? 1001;
            // $shortYear = date("y");
            // $floor_id = $request->floor_id;
            // $room_id = $request->room_id;
            // $tanentId = Tanent::max('id') + 1;

            // $data["system_user_id"] = "{$communityId}{$shortYear}{$floor_id}{$room_id}{$tanentId}";

            $communityId = $request->company_id;
            $shortYear = date("y");
            $floor_number = $request->floor_number;
            $room_number = $request->room_number;
            $tanentId = Tanent::max('id') + 1;

            $data["system_user_id"] = "{$communityId}{$shortYear}{$floor_number}{$room_number}{$tanentId}";

            if (isset($request->profile_picture)) {
                $file = $request->file('profile_picture');
                $ext = $file->getClientOriginalExtension();
                $fileName = time() . '.' . $ext;
                $request->file('profile_picture')->move(public_path('/community/profile_picture'), $fileName);
                $data['profile_picture'] = $fileName;
            }

            $documents = [
                'passport_doc',
                'id_doc',
                'contract_doc',
                'ejari_doc',
                'license_doc',
                'others_doc'
            ];

            foreach ($documents as $document) {
                if ($request->hasFile($document)) {
                    $data[$document] = Tanent::ProcessDocument($request->file($document), "/community/$document");
                }
            }

            $record = Tanent::create($data);

            if ($record) {
                return $this->response('Tanent Successfully created.', $record, true);
            } else {
                return $this->response('Tanent cannot create.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tanent  $Tanent
     * @return \Illuminate\Http\Response
     */

    public function validateUpdateTanent(UpdateRequest $request, $id)
    {
        $Tanent = Tanent::where("id", $id)->first();

        $phone_number = $request->phone_number;

        if ($Tanent->phone_number != $phone_number) {
            $exists = Tanent::where("company_id", $request->company_id)->where('phone_number', $phone_number)->exists();

            // Check if the Tanent number already exists
            if ($exists) {
                return $this->response('Tanent already exists.', null, true);
            }
        }

        return $this->response('Tanent successfully updated.', null, true);
    }


    public function tanentUpdate(UpdateRequest $request, $id)
    {
        $Tanent = Tanent::where("id", $id)->first();

        $phone_number = $request->phone_number;

        if ($Tanent->phone_number != $phone_number) {
            $exists = Tanent::where("company_id", $request->company_id)->where('phone_number', $phone_number)->exists();

            // Check if the Tanent number already exists
            if ($exists) {
                return $this->response('Tanent already exists.', null, true);
            }
        }

        try {

            $data = $request->validated();

            $data["full_name"] = "{$data["first_name"]} {$data["last_name"]}";

            if (isset($request->profile_picture)) {
                $file = $request->file('profile_picture');
                $ext = $file->getClientOriginalExtension();
                $fileName = time() . '.' . $ext;
                $request->file('profile_picture')->move(public_path('/community/profile_picture'), $fileName);
                $data['profile_picture'] = $fileName;
            }


            $documents = [
                'passport_doc',
                'id_doc',
                'contract_doc',
                'ejari_doc',
                'license_doc',
                'others_doc'
            ];

            foreach ($documents as $document) {
                if ($request->hasFile($document)) {
                    $data[$document] = Tanent::ProcessDocument($request->file($document), "/community/$document");
                }
            }

            $record = $Tanent->update($data);

            return $this->response('Tanent successfully updated.', $record, true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tanent  $Tanent
     * @return \Illuminate\Http\Response
     */

    public function destroy(Tanent $Tanent)
    {
        try {
            if ($Tanent->delete()) {
                return $this->response('Tanent successfully deleted.', null, true);
            } else {
                return $this->response('Tanent cannot delete.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
