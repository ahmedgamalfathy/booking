<?php

namespace App\Services\Client;
use App\Models\Client\Client;
use App\Filters\Client\FilterClient;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;


class ClientService
{
       public function allClients()
    {
        $perPage = request()->get('pageSize', 10);
        $clients = QueryBuilder::for(Client::class)
        ->allowedSorts('created_at')
        ->allowedFilters([
            AllowedFilter::custom('search', new FilterClient()),
        ])->orderBy('created_at', 'desc')
        ->paginate($perPage); // Pagination applied here
        return $clients;
    }
    public function editClient(int $id)
    {
        return Client::with(['emails', 'phones', 'addresses'])->find($id);
    }
    public function createClient(array $data): Client
    {
            $client=Client::create([
                'name'=>$data['name'],
                'note'=>$data['note'],
                'type'=>$data['type'],
            ]);
            if (!empty($data['emails'])) {
                foreach ($data['emails'] as $email) {
                    $client->emails()->create(
                        [
                            'email' => $email['email'],
                            'is_main' => $email['isMain']
                        ]);
                }
            }

            if (!empty($data['phones'])) {
                foreach ($data['phones'] as $phone) {
                    $client->phones()->create([
                        'phone' => $phone['phone'],
                        'is_main' => $phone['isMain'],
                        'country_code' => $phone['countryCode'],
                    ]);
                }
            }

            if (!empty($data['addresses'])) {
                foreach ($data['addresses'] as $address) {
                    $client->addresses()->create(
                        [
                            'address' => $address['address'],
                            'is_main' => $address['isMain'],
                            'city' => $address['city'] ?? null,
                        ]);
                }
            }

      return $client;
    }
    public function updateClient(int $id, array $data )
    {
        $client = Client::find($id);
        $client->update([
            'name'=>$data['name'],
            'note'=>$data['note'],
            'type'=>$data['type'],
        ]);
        return $client;
    }
    public function deleteClient(int $id)
    {
        $client = Client::findOrFail($id);
        $client->emails()->delete();
        $client->phones()->delete();
        $client->addresses()->delete();
        $client->delete();
    }
    public function restoreClient($id)
    {
        $client = Client::withTrashed()->findOrFail($id);
        $client->emails()->withTrashed()->restore();
        $client->phones()->withTrashed()->restore();
        $client->addresses()->withTrashed()->restore();
        $client->restore();
    }

    public function forceDeleteClient($id)
    {
        $client = Client::withTrashed()->findOrFail($id);
        $client->emails()->withTrashed()->forceDelete();
        $client->phones()->withTrashed()->forceDelete();
        $client->addresses()->withTrashed()->forceDelete();
        $client->forceDelete();
    }

}
