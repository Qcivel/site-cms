const button = document.getElementById("addTodo");
const message = document.getElementById("msg-feedback");


async function saveTask(){

    const HEADERS ={
        method : "POST",
        mode: "cors",
        headers: {
            "Content-Type": "application/json"
        },
        body : JSON.stringify({
            "title_todo" : document.getElementById("title_todo").value,
            "text_todo" : document.getElementById("text_todo").value,
            "importance_todo" : document.getElementById("importance_todo").value,
            "id_user" : document.getElementById("id_user").value,
        })
    }
    try{
        const response = await fetch('http://localhost/site-cms/api/api_todo.php',HEADERS);
        const data = await response.json();
        console.log(data);
        if (response.ok) {
            message.classList.add("success");
            message.textContent = data.message;
            // On rafraîchit la liste immédiatement
            displayTask(); 
            document.getElementById("title_todo").value = "";
            document.getElementById("text_todo").value = "";
            
        }

    } catch (error) {
        message.textContent ="Erreur lors de la sauvegarde:", error;
    }
    
}

button.addEventListener("click",function(event){
    event.preventDefault(); //On empêche le formulaire de recharger la page
    saveTask();
});

async function displayTask(){
    try{
        const response = await fetch('http://localhost/site-cms/api/get_all_task.php');

        const tasks = await response.json();
        const taskContainer = document.getElementById("task");

        tasks.forEach(task => {

            let importanceClass = "";

        if (task.importance_todo == 3) {
            importanceClass = "high-priority";
        } else if (task.importance_todo == 2) {
            importanceClass = "medium-priority";
        } else {
            importanceClass = "low-priority";
        }

        const li = document.createElement("li");
        const h3 = document.createElement("h3");
        const h4 = document.createElement("h4");
        const p = document.createElement("p");
        const br = document.createElement("br");
        const dltButton = document.createElement("button");

        taskContainer.appendChild(li);
        li.classList.add(importanceClass);
        li.appendChild(h3);
        li.appendChild(h4);
        li.appendChild(p);
        li.appendChild(dltButton);
        li.appendChild(br);


        h3.textContent=task.title_todo;
        h4.textContent=task.text_todo;
        p.textContent="Priorité :" + task.importance_todo;
        dltButton.textContent="Supprimer";

        dltButton.addEventListener("click",function(event){
    event.preventDefault(); //On empêche le formulaire de recharger la page
    deleteTask(task.id_todo);
});

            
        });
    }catch (error) {
        console.log("Erreur lors de la récupération:", error);
    }
}
// On écoute le chargement du document
document.addEventListener("DOMContentLoaded", function() {
    // On appelle la fonction pour afficher les tâches stockées en BDD
    displayTask();
});
