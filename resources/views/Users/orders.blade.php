@extends('layouts.app')

@section('content')
    <section class="home-slider owl-carousel">

        <div class="slider-item" style="background-image: url({{ asset('assets/images/bg_3.jpg') }});"
            data-stellar-background-ratio="0.5">
            <div class="overlay"></div>
            <div class="container">
                <div class="row slider-text justify-content-center align-items-center">

                    <div class="col-md-7 col-sm-12 text-center ftco-animate">
                        <h1 class="mb-3 mt-5 bread">My Orders</h1>
                        <p class="breadcrumbs"><span class="mr-2"><a href="index.html">Home</a></span> <span>My
                                Orders</span>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section ftco-cart">
        <div class="container">
            <div class="row">
                <div class="col-md-12 ftco-animate">
                    <div class="cart-list">
                        <table class="table">
                            <thead class="thead-primary">
                                <tr class="text-center">
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    {{-- <th>Address</th> --}}
                                    <th>City</th>
                                    <th>Email</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Write review</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if ($orders->count() > 0)
                                    @foreach ($orders as $order)
                                        <tr class="text-center">
                                            <td class="total">{{ $order->first_name }}</td>

                                            <td class="total">
                                                {{ $order->last_name }}
                                            </td>

                                            {{-- <td class="total">
                                                {{ $order->address }}
                                            </td> --}}

                                            <td class="price">{{ $order->city }}</td>

                                            <td class="total">
                                                {{ $order->email }}
                                            </td>

                                            <td class="total">{{ $order->price }}</td>
                                            <td class="total">{{ $order->status }}</td>
                                            <td class="total">
                                                @if ($order->status == 'Delivered')
                                                    <a class="btn btn-primary" href="{{ route('write.reviews') }}">Write
                                                        review</a>
                                                @else
                                                    <p> not available just yet</p>
                                                @endif
                                            </td>
                                        </tr><!-- END TR-->
                                    @endforeach
                                @else
                                    <p class="alert alert-success"> You have no orders just yet</p>
                                @endif

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
