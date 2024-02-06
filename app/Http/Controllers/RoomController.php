<?php

namespace App\Http\Controllers;

use App\Http\Requests\Room\StoreRequest;
use App\Http\Requests\Room\UpdateRequest;

use App\Models\Room;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Room::where("company_id", request("company_id"))->with(["floor", "room_category"])->paginate(request("per_page") ?? 10);
    }

    public function getRoomsByFloorId()
    {
        return Room::where("company_id", request("company_id"))->where("floor_id", request("floor_id"))->get(["id","room_number"]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        try {
            $exists = Room::where("company_id", $request->company_id)->where('room_number', $request->room_number)->exists();

            // Check if the room number already exists
            if ($exists) {
                return $this->response('Room already exists.', null, true);
            }

            $record = Room::create($request->validated());

            if ($record) {
                return $this->response('Room Successfully created.', $record, true);
            } else {
                return $this->response('Room cannot create.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Room $room)
    {
        $newRoomNumber = $request->room_number;

        // If the room number is different from the updated value
        if ($room->room_number !== $newRoomNumber) {
            $exists = Room::where("company_id", $request->company_id)->where('room_number', $newRoomNumber)->exists();

            // Check if the room number already exists
            if ($exists) {
                return $this->response('Room already exists.', null, true);
            }
        }

        try {
            // If the room number is the same or it's unique, update the room
            $record = $room->update($request->validated());

            return $this->response('Room successfully updated.', $record, true);
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */

    public function destroy(Room $room)
    {
        try {
            if ($room->delete()) {
                return $this->response('Room successfully deleted.', null, true);
            } else {
                return $this->response('Room cannot delete.', null, false);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
