<?php

namespace App\Http\Controllers;

use App\Enums\ActionStatus;
use App\Enums\RegistrationStatus;
use App\Enums\Shift;
use App\Models\Vaccine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user()->user;
        $vaccines = Vaccine::isAllow()->get();

        $registrations = $user->schedules();

        if ($request->from_date !== null) {
            $registrations = $registrations->whereDate('on_date', '>=', $request->from_date);
        }

        if ($request->to_date !== null) {
            $registrations = $registrations->whereDate('on_date', '<=', $request->to_date);
        }

        if ($request->vaccine_id !== null) {
            $registrations = $registrations->whereRelation('vaccineLot', 'vaccine_id', $request->vaccine_id);
        }

        if ($request->shift !== null) {
            $registrations = $registrations->wherePivot('shift', $request->shift);
        }

        if ($request->status !== null) {
            $registrations = $registrations->wherePivot('status', $request->status);
        } else {
            $registrations = $registrations->wherePivotNotIn('status', [RegistrationStatus::CANCELED]);
        }

        $registrations = $registrations
            ->orderBy('status', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(config('parameters.DEFAULT_PAGINATING_NUMBER'));

        return view('user.registration.index', [
            'registrations' => $registrations,
            'vaccines' => $vaccines,
            'attributes' => $request,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    protected function checkCurrentStatus($currentStatus)
    {
        // User can only cancel the registration
        // => update status from 'registered' to 'canceled'
        if ($currentStatus === RegistrationStatus::REGISTERED) {
            return [
                'check' => true,
                'message' => __('message.success'),
            ];
        }

        return [
            'check' => false,
            'message' => __('registration.message.action_not_allowed'),
        ];
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
        DB::beginTransaction();
        try {
            $user = Auth::user()->user;
            $registration = $user->schedules()->findOrFail($id);
            // dd($registration);
            $checkStatus = $this->checkCurrentStatus($registration->pivot->status);
            if ($checkStatus['check'] === false) {
                DB::rollBack();

                return redirect()->back()->with([
                    'status' => ActionStatus::WARNING,
                    'message' => $checkStatus['message'],
                ], 200);
            }

            switch ($registration->pivot->shift) {
                case Shift::DAY_SHIFT:
                    $registration->day_shift_registration--;

                    break;
                case Shift::NOON_SHIFT:
                    $registration->noon_shift_registration--;

                    break;
                case Shift::NIGHT_SHIFT:
                    $registration->night_shift_registration--;

                    break;
                default:
                    return redirect()->back()->with([
                        'status' => ActionStatus::WARNING,
                        'message' => __('vaccination.invalid_shift'),
                    ]);
            }

            $user->schedules()->updateExistingPivot($id, [
                'status' => RegistrationStatus::CANCELED,
            ]);
            $registration->save();
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with([
                'status' => ActionStatus::ERROR,
                // 'message' => __('message.failed')
                'message' => $e->getMessage(),
            ]);
        }

        DB::commit();

        return redirect()->back()->with([
            'status' => ActionStatus::SUCCESS,
            'message' => __('message.success', ['action' => __('btn.cancel')]),
        ]);
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
