<canvas class="w-full" id="{!! $chartId !!}"></canvas>

@script
    <script>
        const ctx = document.getElementById('{{ $chartId }}');

        const chart = new Chart(ctx, {
            type: '{{ $chartType }}',
            data: {},
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                responsive: true,
                maintainAspectRatio: true
            }
        });

        Livewire.on(`refreshChartData-{!! $chartId !!}`, (data) => {
            chart.data = data[0];
            chart.update();
        });
    </script>
@endscript
