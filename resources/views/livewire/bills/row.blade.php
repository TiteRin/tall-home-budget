<tr>
    <td>{{ $this->bill->name ?? '' }}</td>
    <td>{{ $this->bill->amount->toCurrency() ?? '-' }}</td>
    <td>{{ $this->bill->distribution_method->label() ?? '' }}</td>
    <td>
        @if ($this->bill->member)
            {{ $this->bill->member->full_name ?? '' }}
        @else
            Compte joint
        @endif
    </td>
    <td>
        <button class="btn btn-primary" wire:click="edit">Modifier</button>
        <button class="btn btn-secondary" wire:click="delete">Supprimer</button>
    </td>
</tr>
