<?php

namespace App\Livewire\Admin\Order;

use App\Models\User;
use App\Modules\User\Models\UserAddress;
use Livewire\Component;

/**
 * CustomerAddressSelector Livewire Component
 * 
 * Manages customer selection and address book for admin order creation
 * Features: Customer selection, address book modal, auto-fill phone from profile
 */
class CustomerAddressSelector extends Component
{
    public $selectedUserId;
    public $customerPhone;
    public $savedAddresses;
    public $userProfile;
    public $showAddressModal = false;
    
    // Listeners for parent communication
    protected $listeners = ['customerSelected' => 'loadCustomerData'];

    public function mount()
    {
        $this->savedAddresses = collect([]);
        $this->userProfile = null;
    }

    public function loadCustomerData($userId)
    {
        $this->selectedUserId = $userId;
        
        if ($userId) {
            // Load user profile
            $this->userProfile = User::find($userId);
            
            // Load saved addresses
            $this->savedAddresses = UserAddress::where('user_id', $userId)
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Set phone from profile
            $this->customerPhone = $this->userProfile->mobile ?? $this->userProfile->phone ?? '';
            
            // Dispatch event to parent with customer data
            $this->dispatch('customerDataLoaded', 
                userId: $userId,
                phone: $this->customerPhone,
                addresses: $this->savedAddresses->toArray(),
                profile: [
                    'name' => $this->userProfile->name,
                    'email' => $this->userProfile->email,
                    'phone' => $this->customerPhone,
                    'address' => $this->userProfile->address ?? '',
                ]
            );
        } else {
            // Clear data
            $this->savedAddresses = collect([]);
            $this->userProfile = null;
            $this->customerPhone = '';
        }
    }

    public function openAddressModal()
    {
        if ($this->selectedUserId) {
            $this->showAddressModal = true;
        }
    }

    public function closeAddressModal()
    {
        $this->showAddressModal = false;
    }

    public function selectAddress($name, $phone, $email, $address)
    {
        // Dispatch event to parent to populate shipping form
        $this->dispatch('addressSelected', 
            name: $name,
            phone: $phone,
            email: $email,
            address: $address
        );
        $this->closeAddressModal();
    }

    public function render()
    {
        return view('livewire.admin.order.customer-address-selector');
    }
}
