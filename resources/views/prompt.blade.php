@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-12 col-md-6">
            <!-- Laravel Horizon Card -->
            <div class="card mb-4">
                <div class="row no-gutters">
                    <div class="col-md-12">
                        <div class="card-body">
                            <h5 class="card-title">Horizon</h5>
                            <p class="card-text">Monitoring Queue and Jobs</p>
                            <a href="/insighthub/horizon" class="btn btn-primary">Go to Horizon</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Laravel Telescope Card -->
            <div class="card mb-4">
                <div class="row no-gutters">
                    <div class="col-md-12">
                        <div class="card-body">
                            <h5 class="card-title">Telescope</h5>
                            <p class="card-text">Debug your applications with Telescope.</p>
                            <a href="/insighthub/telescope" class="btn btn-primary">Go to Telescope</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
