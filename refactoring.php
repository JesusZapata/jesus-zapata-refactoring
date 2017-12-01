<?php

public function post_confim() {
    $id = Input::get('service_id');
    $service = Service::find($id);
    if (!$service) {
        return Response::json(array('error' => Error::ServiceNotFound));
    }
    if ($service->status_id == '6') {
        return Response::json(array('error' => '2'));
    }
    if ($service->driver_id == NULL && $service->status_id == '1') {
        $services = Service::update($id, array(
                        'driver_id' => Input::get('driver_id'),
                        'status_id' => '2'
                        //Up Carro
                        //,'pwd' => md5(Input::get('pwd'))
        ));
        Driver::update(Input::get('driver_id'), array(
            "available" => '0'
        ));
        $driverTmp = Driver::find(Input::get('driver_id'));
        Service::update($id, array(
            'car_id' => $driverTmp->car_id
        ));
        //  Notify to the user
        $pushMessage = 'Tu servicio ha sido confirmado!';
        /*  $service = Service::find($id);
            $push = Push::make();
            if ($service->user->type == '1) {//iPhone
            $pushAns = $push->ios($service->user->uuid, $pushMessage);
            } else {
            $pushAns = $push->android($service->user->uuid, $pushMessage);
            }*/
        $service = Service::find($id);
        $push = Push::make();
        if ($service->user-uuid == '') {
            return Response::json(array('error' => '0'));
        }
        if ($service->user->type == '1') {//iphone
            $result = $push->ios($service->user->uuid, $pushMessage, 1, 'honk.wav', 'Open', array('serviceId' => $service->id));
        } else {
            $result = $push->android2($service->user->uuid, $pushMessage, 1, 'default', 'Open', array('serviceId' => $service->id));
        }
        return Response::json(array('error' => '0'));
    } else {
        return Response::json(array('error' => '1'));
    } else {
        return Response::json(array('error' => '3'));
    }
}