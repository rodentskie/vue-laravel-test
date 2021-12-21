<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventDetails;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $fields =  $request->validate(
                [
                    'name' => 'required',
                    'days' => 'required',
                    'from_date' => 'required',
                    'to_date' => 'required',
                ],
                [
                    'name.required' => 'Please enter an event name!',
                    'days.required' => 'Please enter a days!',
                    'from_date.required' => 'Please enter start date!',
                    'to_date.required' => 'Please enter end date!',
                ]
            );

            $from = Carbon::createFromFormat('Y-m-d', $fields['from_date']);
            $to = Carbon::createFromFormat('Y-m-d', $fields['to_date']);

            $result = $to->lt($from);

            if ($result == 1) {
                $response = [
                    'msg' => 'Invalid date range!',
                ];

                return response($response, 400);
            }

            DB::beginTransaction();

            $head = Event::create([
                'name' => $fields['name']
            ]);

            EventDetails::create([
                'event_id' => $head['id'],
                'days' => $fields['days'],
                'from_date' => $fields['from_date'],
                'to_date' => $fields['to_date']
            ]);

            DB::commit();

            $response = [
                'msg' => 'Event successfully created!',
            ];

            return response($response, 201);
        } catch (\Exception $e) {
            DB::rollback();
            // error_log($e);
            $response = [
                'msg' => 'Error inserting the data!',
            ];

            return response($response, 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
