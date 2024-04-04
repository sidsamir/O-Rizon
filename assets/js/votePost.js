document.addEventListener('DOMContentLoaded', function() {
    console.log('votePost.js chargé !')
    document.querySelectorAll('.vote-btn').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const voteType = this.dataset.voteType;

            // Utilisez l'URL absolue pour la requête
            const url = `/trip/post/${postId}/vote`;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ voteType: voteType })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        document.querySelector(`#post-vote-${postId}`).textContent = data.vote;
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });
});