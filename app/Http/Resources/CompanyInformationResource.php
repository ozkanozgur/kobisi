<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id"            => $this->id,
            "site_url"      => $this->site_url,
            "name"          => $this->name,
            "last_name"     => $this->last_name,
            "company_name"  => $this->company_name,
            "email"         => $this->email,
            "created_at"    => $this->created_at,
            "package"       => [
                "id"            => $this->package->id,
                "name"          => $this->package->package->name,
                "price"         => $this->package->package->price,
                "period"        => $this->package->package->period,
                "status"        => $this->package->status,
                "start_date"    => $this->package->start_date,
                "end_date"      => $this->package->end_date,
            ],
        ];
    }
}
