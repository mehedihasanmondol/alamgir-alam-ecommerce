<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Modules\User\Models\UserAddress;
use Illuminate\Support\Facades\Auth;

/**
 * AddressManager Livewire Component
 * 
 * Manages customer addresses with CRUD operations
 * Features: Add, Edit, Delete, Set Default
 */
class AddressManager extends Component
{
    public $addresses;
    public $showModal = false;
    public $showDeleteModal = false;
    public $editMode = false;
    public $addressId;
    public $addressToDelete = null;

    // Form fields
    public $label;
    public $name;
    public $phone;
    public $email;
    public $address;
    public $is_default = false;

    protected $listeners = ['openAddressModal' => 'openModal'];

    protected function rules()
    {
        return [
            'label' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'is_default' => 'boolean',
        ];
    }

    public function mount()
    {
        $this->loadAddresses();
    }

    public function loadAddresses()
    {
        $this->addresses = UserAddress::where('user_id', Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($addressId)
    {
        $address = UserAddress::where('user_id', Auth::id())->findOrFail($addressId);
        
        $this->addressId = $address->id;
        $this->label = $address->label;
        $this->name = $address->name;
        $this->phone = $address->phone;
        $this->email = $address->email;
        $this->address = $address->address;
        $this->is_default = $address->is_default;
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->editMode) {
                $address = UserAddress::where('user_id', Auth::id())->findOrFail($this->addressId);
                $address->update($this->getFormData());
                session()->flash('success', 'Address updated successfully!');
            } else {
                UserAddress::create(array_merge(
                    $this->getFormData(),
                    ['user_id' => Auth::id()]
                ));
                session()->flash('success', 'Address added successfully!');
            }

            // If this address is set as default, remove default from others
            if ($this->is_default) {
                $this->setAsDefault($this->addressId ?? UserAddress::where('user_id', Auth::id())->latest()->first()->id);
            }

            $this->closeModal();
            $this->loadAddresses();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save address. Please try again.');
        }
    }

    public function confirmDelete($addressId)
    {
        $this->addressToDelete = $addressId;
        $this->showDeleteModal = true;
    }

    public function deleteAddress()
    {
        if ($this->addressToDelete) {
            try {
                $address = UserAddress::where('user_id', Auth::id())->findOrFail($this->addressToDelete);
                $address->delete();
                
                $this->loadAddresses();
                session()->flash('success', 'Address deleted successfully!');
            } catch (\Exception $e) {
                session()->flash('error', 'Failed to delete address.');
            }
        }

        $this->showDeleteModal = false;
        $this->addressToDelete = null;
    }

    public function setAsDefault($addressId)
    {
        try {
            // Remove default from all addresses
            UserAddress::where('user_id', Auth::id())->update(['is_default' => false]);
            
            // Set this address as default
            $address = UserAddress::where('user_id', Auth::id())->findOrFail($addressId);
            $address->update(['is_default' => true]);
            
            $this->loadAddresses();
            session()->flash('success', 'Default address updated!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update default address.');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'addressId',
            'label',
            'name',
            'phone',
            'email',
            'address',
            'is_default',
            'editMode'
        ]);
    }

    private function getFormData()
    {
        return [
            'label' => $this->label,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'is_default' => $this->is_default,
        ];
    }

    public function render()
    {
        return view('livewire.customer.address-manager');
    }
}
