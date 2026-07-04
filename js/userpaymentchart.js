document.addEventListener('DOMContentLoaded', function() {

            const labels = ['Vaccination', 'Flea Treatment', 'Deworming', 'Consultation'];
            const values = [50, 25, 30, 40];
            const total = values.reduce((sum, val) => sum + val, 0);
            document.getElementById('totalAmount').textContent = total;
            const ctx = document.getElementById('paymentChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Price ($)',
                        data: values,
                        backgroundColor: '#4CAF50'
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });