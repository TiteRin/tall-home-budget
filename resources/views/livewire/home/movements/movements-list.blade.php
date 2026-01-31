<section class="card bg-base-100 shadow-xl w-full">
    <div class="card-body">
        <h2 class="card-title">
            Mouvements
        </h2>
        <ul class="list">
            @foreach($movements as $movement)
                <livewire:home.movements.movement-item
                    :from="$movement->memberFrom"
                    :to="$movement->memberTo"
                    :amount="$movement->amount"/>
            @endforeach
        </ul>
    </div>
</section>
