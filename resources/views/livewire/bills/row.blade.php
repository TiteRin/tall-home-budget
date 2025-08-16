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
        <button class="btn btn-primary">Modifier</button>
        <button class="btn btn-secondary">Supprimer</button>
    </td>
</tr>
