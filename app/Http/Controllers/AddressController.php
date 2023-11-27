<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{

    private function getContact($user, $idContact)
    {
        $contact = Contact::where('user_id', $user)->where('id', $idContact)->first();
        if (!$contact) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ], 404));
        }

        return $contact;
    }

    private function getAddress($idContact, $idAddress)
    {
        $address = Address::where('contact_id', $idContact)->where('id', $idAddress)->first();
        if (!$address) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ], 404));
        }

        return $address;
    }

    public function create($idContact, AddressCreateRequest $request)
    {
        $contact = $this->getContact(auth()->user()->id, $idContact);

        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function get($idContact, $idAddress)
    {
        $contact = $this->getContact(auth()->user()->id, $idContact);
        $address = $this->getAddress($contact->id, $idAddress);
        return new AddressResource($address);
    }

    public function update($idContact, $idAddress, AddressUpdateRequest $request)
    {
        $user = Auth::user();
        $contact = $this->getContact($user->id, $idContact);
        $address = $this->getAddress($contact->id, $idAddress);

        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }

    public function delete($idContact, $idAddress)
    {
        $contact = $this->getContact(auth()->user()->id, $idContact);
        $address = $this->getAddress($contact->id, $idAddress);
        $address->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function list($idContact)
    {
        $contact = $this->getContact(auth()->user()->id, $idContact);

        $addresses = Address::where('contact_id', $contact->id)->get();
        return (AddressResource::collection($addresses))->response()->setStatusCode(200);
    }
}
