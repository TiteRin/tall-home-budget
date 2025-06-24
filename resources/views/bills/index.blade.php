<section>
    <h1>Les dépenses du foyer</h1>
    @if ($bills->isEmpty())
        <p>Aucune dépense</p>
    @else
        <ul>
            @foreach ($bills as $bill)
                <li>{{ $bill->name }}</li>
            @endforeach
        </ul>
    @endif
</section>
