<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Model;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    function handleRecordCreation(array $data): Model
    {
        return Order::createOrder($data);
    }

    function getHeaderActions(): array
    {
        return [
            Action::make('createCustomer')
                ->label('New customer')
                ->color('info')
                ->icon('heroicon-m-user')
                ->modalHeading('Add new customer')
                ->modalWidth(MaxWidth::Medium)
                ->form([
                    TextInput::make('phone')->required()->minLength(9)->maxLength(15)->tel()->unique()->autocomplete(false),
                    TextInput::make('name')->nullable()->maxLength(255)->autocomplete(false),
                    Textarea::make('address')->nullable(),
                ])
                ->action(function (array $data, Customer $customer) : void {
                    $customer->phone = $data['phone'];
                    $customer->name = $data['name'];
                    $customer->address = $data['address'];
                    $customer->save();
                    // dd($data);
                })
                ->model(Customer::class)
        ];
    }
}
