document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.toggle-state').forEach(container => {
        container.addEventListener('click', function() {
            const checklistId = this.getAttribute('data-id');
            const img = this.querySelector('img'); // Sélectionner l'image à l'intérieur du div
            const checkUrl = this.getAttribute('data-check-url');
            const uncheckUrl = this.getAttribute('data-uncheck-url');

            fetch(`/trip/checklist/${checklistId}/toggle-state`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                // Mettre à jour l'image en fonction de l'état retourné
                img.src = data.state ? checkUrl : uncheckUrl;
            })
            .catch(error => console.error('Error:', error));
        });
    });

    document.querySelectorAll('.delete-task').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Pour éviter de suivre le lien
            const taskId = this.getAttribute('data-id');
            console.log(taskId)

            if (confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
                fetch(`/checklist/${taskId}/delete`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        '_method': 'DELETE',
                        // Symfony permet d'émuler la méthode DELETE avec POST
                    }),
                })
                    .then(response => {
                        if (response.ok) {
                            window.location.reload(); // Recharger la page pour voir les changements
                        } else {
                            alert('Une erreur est survenue lors de la suppression de la tâche.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    });
});