<x-app-layout>
    <section>
        <h1>Les dépenses du foyer</h1>
        @if ($bills->isEmpty())
            <p>Aucune dépense</p>
        @else
            <ul>
                @foreach ($bills as $bill)
                    <li>{{ $bill->name }} : {{ $bill->amount_formatted }}</li>
                @endforeach
            </ul>
        @endif
        <a href="" class="btn btn-primary">Ajouter une dépense</a>
    </section>
</x-app-layout>
