<?php

namespace App\Livewire;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Member;
use App\Repositories\BillRepository;
use App\Services\HouseholdService;
use App\Traits\HasCurrencyFormatting;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Prop;
use Livewire\Component;

class BillForm extends Component
{

    use HasCurrencyFormatting;

    #[Prop]
    public DistributionMethod $defaultDistributionMethod;
    #[Prop]
    public Collection $householdMembers;
    #[Prop]
    public bool $hasJointAccount = true;

    private BillRepository $billRepository;
    private HouseholdService $householdService;

    public string $newName = '';
    public int $newAmount;
    public string $formattedNewAmount;
    public string $newDistributionMethod;
    public int|null $newMemberId;

    public function boot(BillRepository $billRepository, HouseholdService $householdService): void
    {
        $this->billRepository = $billRepository;
        $this->householdService = $householdService;
    }

    public function mount(): void
    {
        $this->newDistributionMethod = ($this->defaultDistributionMethod ?? DistributionMethod::EQUAL)->value;
        $this->householdMembers = $this->householdMembers ?? collect();
    }

    protected function rules(): array
    {
        return [
            'newName' => 'required|string|min:1',
            'newAmount' => [
                'required',
                'gt:0',
                function (string $attribute, string $value, Closure $fail) {
                    if ($this->formatCurrency($value) === $this->formattedNewAmount) return;
                    $fail("Le champ $attribute n'est pas valide.");
                }
            ],
            'formattedNewAmount' => 'required|string|min:1',
            'newDistributionMethod' => 'required|in:' . implode(",", DistributionMethod::values()),
            'newMemberId' => [
                $this->hasJointAccount ? 'nullable' : 'required',
                'integer',
                'in:' . implode(",", $this->householdMembers->pluck('id')->toArray()),
            ]
        ];
    }

    protected function messages(): array
    {
        return [
            'newName.required' => 'Le champ ":attribute" est requis.',
            'newName.min' => 'Le champ ":attribute" ne peut pas être vide',
            'newAmount.required' => 'Le champ ":attribute" est requis.',
            'newAmount.gt' => 'Le champ ":attribute" doit être supérieur à zéro.',
            'newDistributionMethod.required' => 'Le champ ":attribute" est requis.',
            'newDistributionMethod.in' => 'Le champ ":attribute" n\'est pas valide.',
            'newMemberId.in' => 'Le champ ":attribute" n\'est pas valide.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'newName' => 'Nouvelle dépense',
            'newAmount' => 'Montant',
            'newDistributionMethod' => 'Méthode de distribution',
            'newMemberId' => 'Membre du foyer'
        ];
    }

    public function render(): View
    {
        return view('livewire.bill-form');
    }

    public function submit(): void
    {
        $this->validate();

        $household = $this->householdService->getCurrentHousehold();
        if (!$household) {
            return;
        }

        $distributionMethod = DistributionMethod::from($this->newDistributionMethod);
        $amount = new Amount($this->newAmount);

        $this->billRepository->create(
            $this->newName,
            $amount,
            $distributionMethod,
            $household->id,
            $this->newMemberId
        );

        // Reset form fields after successful submission
        $this->reset(['newName', 'formattedNewAmount']);
        $this->newAmount = 0;
    }

    public function updatedFormattedNewAmount(string $newAmount): void
    {
        if (!is_numeric($newAmount)) {
            $this->newAmount = -1;
            return;
        }
        $this->newAmount = (int)round((float)$newAmount * 100);
        $this->formattedNewAmount = $this->formatCurrency($this->newAmount);
    }

    public function getDistributionMethodOptionsProperty(): array
    {
        return DistributionMethod::options();
    }

    public function getHouseholdMemberOptionsProperty(): array
    {
        return $this->householdMembers
            ->mapWithKeys(
                function (Member $member) {
                    return [$member->id => $member->full_name];
                }
            )->toArray();
    }
}
