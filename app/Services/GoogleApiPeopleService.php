<?php

namespace App\Services;

use Google\Client;
use Google\Service\PeopleService;
use Illuminate\Support\Facades\Http;
use Google\Service\PeopleService\BatchCreateContactsRequest;

class GoogleApiPeopleService
{
    protected $people_service;

    public function __construct()
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . config('app.google_api.google_api_credn'));
        $subject_email = config('app.google_api.subject_email');
        $client = new Client();
        $client->useApplicationDefaultCredentials();
        $scopes = [
            PeopleService::CONTACTS,
            PeopleService::USERINFO_PROFILE,
            PeopleService::CONTACTS_READONLY
          ];

        $client->addScope($scopes);
        $client->setSubject($subject_email);
        $this->people_service = new PeopleService($client);
    }

    public function getAllContacts()
    {
        $allcontacts = $this->people_service->people_connections->listPeopleConnections(
            'people/me',
            array('personFields' => 'names,emailAddresses','pageSize' => 2000,)
        );

        $allcontactsnames = [];
        $allcontactsemail = [];

        foreach ($allcontacts->getConnections() as $key => $connection) {
            $allcontactsnames[] = $connection->getNames()[0]->displayName;
            $allcontactsemail[] = $connection->getEmailAddresses()[0]->value;
        }
        $i = 1;
        while ($allcontacts->getNextPageToken() != null) {
            echo $i++;
            $allcontacts = $this->people_service->people_connections->listPeopleConnections(
                'people/me',
                array('personFields' => 'names,emailAddresses','pageSize' => 2000, 'pageToken' => $allcontacts->getNextPageToken())
            );

            foreach ($allcontacts->getConnections() as $key => $connection) {
                $allcontactsnames[] = $connection->getNames()[0]->displayName;
                $allcontactsemail[] = $connection->getEmailAddresses()[0]->value;
            }
        }
        return ['names' => $allcontactsnames, 'emails' => $allcontactsemail];
    }
    public function batchCreateContact($givenContacts)
    {
        $allcontactsemail = [];
        $allPersons = [];
        $sentContacts = [];
        $batchRequest = new BatchCreateContactsRequest();

        foreach ($givenContacts as $key => $givenContact) {
            if(!in_array($givenContact['email'], $allcontactsemail)) {
                $person = [
                    'contactPerson' => [
                    'names' => [['givenName' => $givenContact['name']]],
                    'emailAddresses' => [['value' => $givenContact['email']]],
                    'phoneNumbers' => [['value' => $givenContact['phn']]]
                    ]
                ];
                $allcontactsemail[] = $givenContact['email'];
                $sentContacts[$givenContact['email']] = ['id' => $givenContact['id']];
                $allPersons[] = $person;
            } else {
                $alreadyfound[] = $givenContact['email'];
            }

        }

        $batchRequest->setContacts($allPersons);
        // Use readMask to specify the values that the API response will return.
        $batchRequest->setReadMask("emailAddresses");
        // Perform the batchRequest
        $results = $this->people_service->people->batchCreateContacts($batchRequest);

        if (count($results->getCreatedPeople()) == 0) {
        } else {
            foreach ($results->getCreatedPeople() as $person) {
                $resourceName = $person->getPerson()->resourceName;
                $emailAddresses = $person->getPerson()->getEmailAddresses()[0]->value;
                $sentContacts[$emailAddresses]['rec_id'] = str_replace('people/', '', $resourceName);
            }

        }
        return $sentContacts;
    }


}
