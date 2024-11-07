<canvas class="w-full max-h-90" id="{!! $chartId !!}"></canvas>

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
                    },
                    x: {
                        beginAtZero: true
                    },
                },
                indexAxis: '{{ $barType }}',
                responsive: true,
                maintainAspectRatio: false
            }
        });

        Livewire.on(`refreshChartData-{!! $chartId !!}`, (data) => {
            chart.data = data[0];
            chart.update();
        });
    </script>
@endscript
