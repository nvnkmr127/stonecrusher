<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Edit Attendance Entry') }}
        </h2>
    </x-slot>

    <div class="row row-cards">
        <div class="col-12">
            <x-card>
                <form method="POST" action="{{ route('attendance.update', $attendance) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <x-form.select
                                name="employee_id"
                                label="Staff Member"
                                :options="$employees->pluck('name', 'id')->toArray()"
                                :selected="old('employee_id', $attendance->employee_id)"
                                required
                            />
                        </div>
                        <div class="col-md-6">
                            <x-form.input
                                name="date"
                                label="Date"
                                type="date"
                                :value="old('date', $attendance->date)"
                                required
                            />
                        </div>
                    </div>



                    <div class="row">
                         <div class="col-md-6">
                            <x-form.select
                                name="status"
                                label="Status"
                                :options="['present'=>'Present', 'late'=>'Late', 'half_day'=>'Half Day', 'leave'=>'Leave', 'absent'=>'Absent']"
                                :selected="old('status', $attendance->status)"
                                required
                            />
                        </div>
                    </div>

                    <x-form.textarea
                        name="remarks"
                        label="Remarks"
                        rows="3"
                        :value="old('remarks', $attendance->remarks)"
                    />

                    <div class="card-footer text-end">
                        <x-button type="submit" variant="primary">
                            {{ __('Update Record') }}
                        </x-button>
                        <a href="{{ route('attendance.index') }}" class="btn btn-link link-secondary">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-tabler-layout>