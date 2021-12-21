<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
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
        try {
            $data = [];


            $head = Event::orderByDesc('id')
                ->take(1)
                ->get();
            if (count($head) == 0) {
                $response = [
                    'data' => $data,
                ];

                return response($response, 200);
            }

            $details = EventDetails::where('event_id', $head[0]['id'])
                ->get();

            $data = array(
                'head' => $head,
                'details' => $details
            );

            $response = [
                'data' => $data,
            ];

            return response($response, 200);
        } catch (\Throwable $th) {
            //throw $th;
            error_log($th);
            $response = [
                'msg' => 'Encountered error on retrieving data, please try again!',
            ];

            return response($response, 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEventRequest $fields)
    {
        try {

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
        } catch (\Throwable $th) {
            // error_log($th);
            DB::rollback();
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
