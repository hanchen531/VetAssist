document.addEventListener('DOMContentLoaded', function() {
            const appointmentLabels = <?php echo json_encode(array_column($appointmentData, 'doctorName')); ?>;
            const appointmentCounts = <?php echo json_encode(array_column($appointmentData, 'count')); ?>;

            new Chart(document.getElementById('appointmentsChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: appointmentLabels,
                    datasets: [{
                        label: 'Appointments',
                        data: appointmentCounts,
                        backgroundColor: '#42a5f5'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });


            const vaccineLabels = <?php echo json_encode(array_column($vaccineData, 'name')); ?>;
            const vaccineCounts = <?php echo json_encode(array_column($vaccineData, 'quantity')); ?>;

            new Chart(document.getElementById('vaccineChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: vaccineLabels,
                    datasets: [{
                        data: vaccineCounts,
                        backgroundColor: ['#66bb6a', '#ef5350', '#ffa726', '#42a5f5', '#ab47bc']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });