  <div class="todo_container">
    <h1>Ma To-Do List</h1>
    <form action="" method="POST">

      <label for="">Titre</label>
      <input type="text" name="title_todo" id="title_todo"  placeholder="Ajouter un titre" required>

      <label for="">Texte</label>
      <input type="text" name="text_todo" id="text_todo"  placeholder="Ajouter un texte" required>

      <label for="">Sélectionner une importance</label>
      <select name="importance_todo" id="importance_todo">
        <option value="1">Priorité : 1</option>
        <option value="2">Priorité : 2</option>
        <option value="3">Priorité : 3</option>
      </select>

      <input type="submit" name="addTodo" id="addTodo">
      <input type="hidden" name="id_user" id="id_user" value="<?php echo $_SESSION['user_id']?>">
      
      <p id="msg-feedback" class="message-info"></p>
      <h3>Liste des taches :</h3>
      <ul id="task"> </ul>
    </form>
  </div>

  