document.addEventListener('DOMContentLoaded', () => {
    const userShowBaseUrl = '/profil/__ID__';
    const searchInput = document.getElementById('userSearch');
    const usersContainer = document.querySelector('.grid');

    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.trim();

        fetch(`/api/users/search?q=${query}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                usersContainer.innerHTML = ''; // Clear previous results
                data.users.forEach(user => {
                    const userElement = `
                            <div class="flex flex-col justify-between items-center bg-gray-100 p-4 rounded-lg mb-4">
                                <img class="w-20 h-20 rounded-full mb-4" src="../images/profil.png" alt="Avatar">
                                <div class="text-center">
                                    <h3 class="text-xl font-bold text-gray-800">${user.username}</h3>
                                    <div class="flex justify-center items-center">
                                        <a href="${userShowBaseUrl.replace('__ID__', user.id)}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full mt-4 text-lg">Voir plus</a>
                                    </div>
                                </div>
                            </div>
                        `;
                    usersContainer.innerHTML += userElement;
                });
            })
            .catch(error => console.error('Error:', error));
    });
});