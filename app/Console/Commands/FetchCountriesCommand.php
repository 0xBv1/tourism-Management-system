<?php

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Str;

class FetchCountriesCommand extends Command
{
    protected $signature = 'countries:fetch';

    protected $description = 'Fetch Countries';

    public function handle(): void
    {
        try {
            $countries = \Http::get('https://restcountries.com/v3.1/independent?fields=name,flag,cca2,idd')->collect()->sortBy('name.common');

            $this->output->progressStart(count($countries));
            $blockedCountries = ['Israel', 'Antarctica', 'Heard Island and McDonald Islands'];

            foreach ($countries as $country) {
                if (in_array($country['name']['common'], $blockedCountries)) {
                    continue;
                }
                $phoneCode = $country['idd']['root'] ?? null;

                if (!is_null($phoneCode) && isset($country['idd']['suffixes'])) {
                    $phoneCode .= $country['idd']['suffixes'][0] ?? '';
                }

                $country = Country::firstOrCreate([
                    'code' => $country['cca2'],
                ], [
                    'name' => $country['name']['common'] ?? null,
                    'flag' => $country['flag'],
                    'code' => $country['cca2'],
                    'phone_code' => $phoneCode,
                ]);
                sleep(5);
                $loadCitiesRequest = Http::post('https://countriesnow.space/api/v0.1/countries/states',[
                    'iso2'=> $country->code
                ]);
                $data = $loadCitiesRequest->json();
                if ($loadCitiesRequest->successful() && isset($data['data']['states'])) {
                    foreach ($data['data']['states'] as $state) {
                        $country->states()->firstOrCreate([
                            'name' => Str::of($state['name'])->remove('Governorate')->trim(),
                            'code' => $state['state_code']
                        ]);
                    }
                }
                $this->output->progressAdvance();
            }
            $this->output->progressFinish();
        } catch (\Throwable $throwable) {
            $this->output->error($throwable->getMessage());
        }
    }
}
