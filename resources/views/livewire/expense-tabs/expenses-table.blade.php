<table class="table">
    <thead>
    <th>
        Dépense
    </th>
    <th>
        Montant
    </th>
    <th>
        Date
    </th>
    <th>
        Mode de partage
    </th>
    <th>
        Compte
    </th>
    </thead>
    <tbody>
    @foreach($expenses as $expense)
        <tr>
            <td>{{ $expense->name }}</td>
            <td>{{ $expense->amount }}</td>
            <td>{{ $expense->date }}</td>
            <td>{{ $expense->distribution_method }}</td>
            <td>{{ $expense->member->full_name }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
