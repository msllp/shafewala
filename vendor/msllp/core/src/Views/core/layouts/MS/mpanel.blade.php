@extends('MS::core.layouts.root')
@section('body')


    <?php
    $data=[

        'path'=>
            [
                'sidebar'=> route('mPanel.SideNav')],
        'accessToken'=> \MS\Core\Helper\Comman::encode('UserMitul')

    ];
    //dd($data);
    $json=collect($data)->toJson();

    ?>




    <msdashboard :ms-data="{{$json}}"></msdashboard>
@endsection
