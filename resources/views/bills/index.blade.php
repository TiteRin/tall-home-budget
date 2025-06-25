<x-app-layout>
    <section>
        <h1>Les dépenses du foyer</h1>
        @if ($bills->isEmpty())
            <p>Aucune dépense</p>
        @else
            <ul>
                @foreach ($bills as $bill)
                    <li>
                        {{ $bill->name }} : {{ $bill->amount_formatted }}
                        <a href="" class="btn btn-primary">Modifier</a>
                        <a href="" class="btn btn-danger">Supprimer</a>
                    </li>
                @endforeach
            </ul>
            <p>
                <strong>Total : {{ $household->total_amount_formatted }}</strong>
            </p>
        @endif
        <a href="" class="btn btn-primary">Ajouter une dépense</a>
    </section>
</x-app-layout>
