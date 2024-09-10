<?php

namespace Geekbrains\Application1\Domain\Controllers;

use Geekbrains\Application1\Application\Render;
use Geekbrains\Application1\Domain\Models\User;

class UserController {

    public function actionIndex(): string {
        $users = User::getAllUsersFromStorage();
        
        $render = new Render();

        if(!$users){
            return $render->renderPage(
                'user-empty.tpl', 
                [
                    'title' => 'Список пользователей в хранилище',
                    'message' => "Список пуст или не найден"
                ]);
        }
        else{
            return $render->renderPage(
                'user-index.tpl', 
                [
                    'title' => 'Список пользователей в хранилище',
                    'users' => $users
                ]);
        }
    }

    public function actionSave(): string {
        if(User::validateRequestData()) {
            $user = new User();
            $user->setParamsFromRequestData();
            $user->saveToStorage();

            $render = new Render();

            return $render->renderPage(
                'user-created.tpl', 
                [
                    'title' => 'Пользователь создан',
                    'message' => "Создан пользователь " . $user->getUserName() . " " . $user->getUserLastName()
                ]);
        }
        else {
            throw new \Exception("Переданные данные некорректны");
        }
    }

    public function actionUpdate(): string {
        if(User::exists($_GET['id'])){ // проверка на существование id пользователя в базе (true/false)
            $user = new User();
            $user->setUserId($_GET['id']); // принимает id пользователя
            
            $arrayData = [];

            if(isset($_GET['name']))
                $arrayData['user_name'] = $_GET['name']; // если в url задано новое имя то поменять на указанное в нем

            if(isset($_GET['lastname'])) {
                $arrayData['user_lastname'] = $_GET['lastname']; // если в url задана новая фамилия то поменять на указанное в нем
            }
            
            $user->updateUser($_GET['id'], $arrayData);
        
        }
        else { 
            throw new \Exception("Пользователь не существует");
        }
        
        $render = new Render();
        return $render->renderPage(
            'user-created.tpl', 
            [
                'title' => 'Пользователь обновлен',
                'message' => "Обновлен пользователь " . $user->getUserId()
            ]);
    }

    public function actionDelete(): string {
        if(User::exists($_GET['id'])) {
            User::deleteFromStorage($_GET['id']);

            $render = new Render();
            
            return $render->renderPage(
                'user-removed.tpl', []
            );
        }
        else {
            throw new \Exception("Пользователь не существует");
        }
    }

    public function actionShow()
    {
        $id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
        if(User::exists($id)) {
            $user = User::getUserFromStorageById($id);
            $render = new Render();
            return $render->renderPage('user-page.tpl',[
                'user' => $user
            ]);
        }
        else {
            throw new \Exception("Пользователь см таким id не существует");
        }
    }
    
}