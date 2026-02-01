<form wire:submit.prevent="submitForm" class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-baseline">
        <label for="" class="label md:justify-end">
            <span class="label-text font-medium">Nom</span>
        </label>
        <div class="md:col-span-2">
            <input type="text"
                   class="input validator"
                   placeholder="Courses, Animaux, Travaux, etc."
                   wire:model="newName"
                   required>
            <p class="label text-sm">Il s’agit du nom qui sera affiché sur la page principal et dans les lignes de
                charges</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-baseline">
        <label for="" class="label md:justify-end">
            <span class="label-text font-medium">Jour de démarrage de la période</span>
        </label>
        <div class="md:col-span-2">
            La période démarre le
            <input type="number"
                   class="input validator w-20"
                   placeholder="1"
                   wire:model="newStartDay"
                   required
                   min="1" max="31"
            />
            du mois.
            <p class="label text-sm">Il s’agit du jour du mois à partir duquel les dépenses seront comptabilisées</p>
        </div>
    </div>

    <div class="flex justify-end mt-4">
        <button type="submit" class="btn btn-primary">Sauvegarder</button>
    </div>

</form>
