<?php

public function post_confim() {
    $id = Input::get('service_id');
    $service = Service::find($id);
    $driver_id = Input::get('driver_id');
    if (!$service) {
        return Response::json(array('error' => Error::ServiceNotFound));
    }
    if ($service->status_id == Estatus::SixStatus) {
        return Response::json(array('error' => Error::Two));
    }
    if (!$service->driver_id && $service->status_id != Estatus::OneStatus) {
        return Response::json(array('error' => Error::One));
    }
    $services = Service::update($id, array(
                    'driver_id' => $driver_id,
                    'status_id' => '2'
    ));
    Driver::update($driver_id, array(
        "available" => '0'
    ));
    $driverTmp = Driver::find($driver_id);
    Service::update($id, array(
        'car_id' => $driverTmp->car_id
    ));
    //  Notify to the user
    $pushMessage = 'Your service has been confirmed!';
    $service = Service::find($id);
    $push = Push::make();
    if ($service->user-uuid == '') {
        return Response::json(array('error' => Error::Zero));
    }
    if ($service->user->type == UserType::Iphone) {
        $result = $push->ios($service->user->uuid,
                             $pushMessage,
                             1,
                             'honk.wav',
                             'Open',
                             array('serviceId' => $service->id));
    } else {
        $result = $push->android2($service->user->uuid,
                                  $pushMessage,
                                  1,
                                  'default',
                                  'Open',
                                  array('serviceId' => $service->id));
    }
    return Response::json(array('error' => Error::Zero));
}