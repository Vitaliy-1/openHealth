<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Exceptions\ApiException;
use App\Classes\eHealth\Request;

class DivisionApi extends Request
{
    public const URL = '/api/divisions';

        /**
     * ONLY FOR TEST PURPOSE
     */


     protected static function getResponse(array $data, string $id = null): array
     {
         $legal_entity_id = auth()->user()->legalEntity->id;
         $legal_entity_uuid = auth()->user()->legalEntity->uuid;
        // dd('fake', $data);
         return [
             "meta" => [
               "code" => 200,
               "url" => "https://example.com/resource",
               "type" => "object",
               "request_id" => "6617aeec-15e2-4d6f-b9bd-53559c358f97#17810"
             ],
             "data" => [
               "id" => $id ?? 'd290f1ee-6c54-4b01-90e6-d701748f0851',
               "name" => $data['name'],
               "addresses" => $data['addresses'],
               "phones"=> $data['phones'],
               "email" => $data['email'],
               "working_hours" => $data['working_hours'],
               "type" => $data['type'],
               "legal_entity_id" => $legal_entity_uuid,
               "external_id" => $data['external_id'] ?? null,
               "location" => $data['location'],
               "status" => "ACTIVE",
               "mountain_group" => false,
               "dls_id" => "2872985",
               "dls_verified" => true
             ]
         ];
     }

     /**
      * END OF ONLY FOR TEST PURPOSE
      */

    public static function _get($data = []): array
    {
        return (new Request('GET', self::URL, $data))->sendRequest();
    }

    public static  function _create($data): array
    {
        // dd('create', $data);
        // return (new Request('POST', self::URL, $data))->sendRequest();
        return self::getResponse($data)['data'];
    }

    /**
     * @throws ApiException
     */
    public static function _update($id, $data): array
    {
        // dd('update', $data);
        // return (new Request('PATCH', self::URL . '/' . $id, $data))->sendRequest();
        return self::getResponse($data, $id)['data'];
    }

    public static function _activate($id): array
    {
        return (new Request('PATCH', self::URL . '/' . $id . '/actions/activate', []))->sendRequest();
    }

    public static function _deactivate($id): array
    {
        return (new Request('PATCH', self::URL . '/' . $id . '/actions/deactivate', []))->sendRequest();
    }

    public static function _sync($legal_entity_id): array
    {
        $data = [
            'legal_entity_id' => $legal_entity_id,
            'page'=> 1,
            'page_size' => 100
        ];
        return (new Request('GET', self::URL, $data))->sendRequest();
    }
}
