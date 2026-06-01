const ctx = document.getElementById('stockChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            { label: 'Issued', data: issued },
            { label: 'Available', data: available }
        ]
    }
});