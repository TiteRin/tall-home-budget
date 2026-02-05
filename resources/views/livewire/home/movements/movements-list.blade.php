<section class="card bg-base-100 shadow-xl w-full md:w-1/2">
    <div class="card-body">
        <h2 class="card-title">
            Mouvements
        </h2>
        <ul class="list">
            @foreach($movements as $index => $movement)
                <livewire:home.movements.movement-item
                    :from="$movement->memberFrom"
                    :to="$movement->memberTo"
                    :amount="$movement->amount"
                    :key="$movement->getId()"
                />
            @endforeach
        </ul>
    </div>
</section>
