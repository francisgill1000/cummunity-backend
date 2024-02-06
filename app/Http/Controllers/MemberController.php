<?php

namespace App\Http\Controllers;

use App\Http\Requests\Member\StoreRequest;

use App\Models\Member;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function memberList($id)
    {
        return Member::where("tanent_id", $id)->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request, $id)
    {
        try {

            $data = $request->validated();

            if (isset($request->profile_picture)) {
                $file = $request->file('profile_picture');
                $ext = $file->getClientOriginalExtension();
                $fileName = time() . '.' . $ext;
                $request->file('profile_picture')->move(public_path('/community/profile_picture'), $fileName);
                $data['profile_picture'] = $fileName;
            }

            Member::create($data);
            return $this->response('Member successfully updated.', null, true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
