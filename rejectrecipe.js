function rejectRecipe(recipeId) {
    fetch('approve_recipe.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: recipeId, status: 'rejected' }),
    })
    .then(response => {
        if (response.ok) {
            alert(`Recipe ${recipeId} rejected and removed.`);
            loadRecipes(); // Reload the recipes to reflect changes
        } else {
            alert('Failed to reject recipe.');
        }
    });
}
