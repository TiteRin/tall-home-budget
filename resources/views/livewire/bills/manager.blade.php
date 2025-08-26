<div>
    <h1>Dépenses du foyer</h1>

    <div id="notification-container" class="mb-4" style="display: none;">
        <div id="notification" class="alert p-4 mb-4 text-sm rounded-lg"></div>
    </div>

    <table class="table">
        <thead>
        <tr>
            <td>Nom</td>
            <td>Montant</td>
            <td>Méthode de distribution</td>
            <td>Qui paie ?</td>
            <td>Actions</td>
        </tr>
        </thead>
        <tbody>
        @forelse($bills as $index => $bill)
            @if ($bill->id === $this->editingBillId && $this->isEditing === true)
                @livewire('bill-form', [
                    'householdMembers' => $this->householdMembers,
                    'hasJointAccount' => $this->hasHouseholdJointAccount,
                    'defaultDistributionMethod' => $this->defaultDistributionMethod,
                    'bill' => $bill
                ])
            @else
                @livewire('bills.row', ['bill' => $bill], key($bill->id))
            @endif
        @empty
            <tr>
                <td colspan="6">
                    Aucune dépense
                </td>
            </tr>
        @endforelse
        </tbody>
        <tfoot>
        @livewire('bill-form', [
            'householdMembers' => $this->householdMembers,
            'hasJointAccount' => $this->hasHouseholdJointAccount,
            'defaultDistributionMethod' => $this->defaultDistributionMethod
        ])
        </tfoot>
    </table>

    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.
            on('notify', (data) => {
                const container = document.getElementById('notification-container');
                const notification = document.getElementById('notification');

                // Set notification content and style
                notification.textContent = data.message;
                notification.className = 'alert p-4 mb-4 text-sm rounded-lg';

                // Add appropriate color class based on type
                if (data.type === 'success') {
                    notification.classList.add('bg-green-100', 'text-green-800');
                } else if (data.type === 'error') {
                    notification.classList.add('bg-red-100', 'text-red-800');
                } else {
                    notification.classList.add('bg-blue-100', 'text-blue-800');
                }

                // Show notification
                container.style.display = 'block';

                // Hide after 3 seconds
                setTimeout(() => {
                    container.style.display = 'none';
                }, 3000);
            });
        });
    </script>
</div>
