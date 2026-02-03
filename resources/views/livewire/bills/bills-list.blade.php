<section class="card bg-base-100 shadow-xl grow w-full">
    <div class="card-body">
        <h2 class="card-title mb-4">
            Charges et Dépenses
        </h2>
        <table class="table table-zebra">
            <thead>
            <tr>
                <th>Charge</th>
                @foreach ($this->members as $member)
                    <th>
                        {{ $member->full_name }}
                    </th>
                @endforeach
                <th>Montant</th>
            </tr>
            </thead>
            <tbody>
            @if (count($bills) > 0)
                @foreach($bills as $bill)
                    <tr>
                        <td>
                            {{ $bill->name }}
                        </td>
                        @foreach ($this->members as $member)
                            <td>
                                @if ($bill->member_id === $member->id)
                                    {{ $bill->amount }}
                                @else
                                    -
                                @endif
                            </td>
                        @endforeach
                        <th>
                            {{ $bill->amount->toCurrency() }}
                        </th>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="2">
                        <a href="{{ route('bills.settings') }}" class="btn btn-primary">
                            Paramétrer les charges
                        </a>
                    </td>
                </tr>
            @endif

            @if (count($this->expenseTabs) > 0)
                @foreach($this->expenseTabs as $tab)
                    <tr>
                        <td>
                            <a href="{{ route('expense-tabs.index', ['tab' => $tab->id]) }}">
                                {{ $tab->name }}
                            </a>
                        </td>
                        @foreach ($this->members as $member)
                            <td>
                                {{ $tab->getExpensesForCurrentPeriod()->getTotalForMember($member) }}
                            </td>
                        @endforeach
                        <td>
                            {{ $tab->getTotalAmountForCurrentPeriod()->toCurrency() }}
                        </td>
                    </tr>
                @endforeach
            @endif

            </tbody>
            <tfoot>
            <tr>
                <th>Total</th>
                @foreach ($this->members as $member)
                    <th>
                        {{ $this->totalAmountForMember($member) }}
                    </th>
                @endforeach
                <th>{{ $this->totalAmount() }}</th>
            </tr>
            </tfoot>
        </table>
    </div>
</section>
