<div class="w-full">
    <div class="w-full">
        <div class="w-full overflow-x-auto">
            <table id="rolesTable" data-update-url="{{ route('roles.update', '__ID__') }}"
                data-destroy-url="{{ route('roles.destroy', '__ID__') }}"
                data-update-permission-url="{{ route('permissions.update', '__ID__') }}"
                data-destroy-permission-url="{{ route('permissions.destroy', '__ID__') }}"
                class="w-full table table-bordered border-collapse">
                <thead>
                    <tr class="bg-bg-chart">
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated here -->
                </tbody>
            </table>
        </div>
    </div>
</div>