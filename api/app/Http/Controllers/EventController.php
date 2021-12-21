<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventDetails;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

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

            $period = CarbonPeriod::create($details[0]['from_date'], $details[0]['to_date']);
            $affected = [];

            // Iterate over the period
            foreach ($period as $date) {
                $eventDays = $details[0]['days'];
                $arr = explode(',', $eventDays);
                $getDay = $date->englishDayOfWeek;
                $bool = in_array($getDay, $arr);

                if ($bool) array_push($affected, $date->format('Y-m-d'));
            }


            $data = array(
                'head' => $head,
                'details' => $details,
                'affected' => $affected
            );

            $response = [
                'data' => $data,
            ];

            return response($response, 200);

            // $mytime = Carbon::now()
            //     ->setTimezone('GMT+8');
            // return response($mytime->toArray(), 200);
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
