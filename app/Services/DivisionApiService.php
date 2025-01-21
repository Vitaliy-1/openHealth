<?php

namespace App\Services;

use App\Traits\WorkTimeUtilities;

class DivisionApiService
{
    use WorkTimeUtilities;

    /**
     * Change divider between hours and minutes
     *
     * @param array $workingHours   // Array with work hours time data
     * @param bool $dotToColon      // Determine how divider must be switched
     *
     * @return array
     */
    public function prepareWorkingHours(array $workingHours, bool $dotToColon = false): array
    {
        return $this->prepareTimeToRequest($workingHours, $dotToColon);
    }

    /**
     * API service requires that values of longitude and latitude should be as number
     * but input fields returns string. Thus this values must be converted to.
     * Otherwise to save Location data comes from API-service the data should be JSON object,
     * therefore it must be converted too.
     *
     * @param array $location // Contains longitude and latitude values
     * @param bool $toJson    // Indicates when location data should be converted to teh JSON object
     *
     * @return string|array
     */
    protected function reformatLocation(array $location, bool $toJson = false): string|array
    {
        if($location && $toJson) {
            return json_encode($location);
        } else {
            $location['longitude'] = (float)$location['longitude'];
            $location['latitude'] = (float)$location['latitude'];

            return $location;
        }
    }

    /**
     * Prepare all incoming raw data to format acceptable for API-service
     *
     * @param array $data // The data comes from Form on the page
     *
     * @return array
     */
    public function prepareRequest(array $data): array
    {
        $params = [
            'name' => $data['name'],
            'type' =>$data['type'],
            'email' => $data['email'],
            'phones' => [$data['phones']], // ESOZ expect to get an array of the objects
            'addresses' => [$data['addresses']], // ESOZ expect to get an array of the addresses,
        ];

        if (!empty($data['external_id'])) {
            $params['external_id'] = $data['external_id'];
        }

        if (!empty($data['location'])) {
            $params['location'] = $this->reformatLocation($data['location']);
        }
        else {
            // This need to set unspecified status if values has already saved in the API's DB
            $params['location']['longitude'] = 0;
            $params['location']['latitude'] = 0;
        }

        if (!empty($data['working_hours'])) {
            $params['working_hours'] = $this->prepareWorkingHours($data['working_hours']);
        }
        else {
            foreach($this->weekdays as $day => $name) {
                $params['working_hours'] = [$day => [["00.00", "00.00"]]];
            }
        }

        return $params;
    }

    /**
     * Prepare all received data from API-service to format acceptable for storing it into teh DB
     *
     * @param array $data // The data comes from the API-service
     *
     * @return array
     */
    public function prepareResponse(array $data): array
    {
        $response = $data;

        if (!empty($data['location']) && (float)$data['location']['longitude'] && (float)$data['location']['latitude']) {
            $response['location'] = $this->reformatLocation($data['location'], true);
        } else {
            // This need to save empty value to the DB
            $response['location'] = null;
        }

        /**
         * Change dot '.'in time values to the  colon ':' as it required by <input type="time">
         * Response contains 'working_hours' time values with dot '.' divider between hours and minutes!
         * If day hasn't schedule it's time will set to the '00.00' because if the working_hours data
         * has been written to the API resource's DB it doesn't accept both empty array and null
         */
        if(!empty($data['working_hours'])) {
            foreach ($this->weekdays as $day => $name) {
                if (isset($data['working_hours'][$day]) &&
                    $data['working_hours'][$day][0][0] === '00.00' &&
                    $data['working_hours'][$day][0][1] === '00.00')
                {
                    unset($data['working_hours'][$day]);
                }
            }

            if (!empty($data['working_hours'])) {
                $response['working_hours'] = $this->prepareWorkingHours($data['working_hours'], true);
            } else {
                // This need to save empty value to teh DB
                $response['working_hours'] = null;
            }
        }

        return $response;
    }
}
