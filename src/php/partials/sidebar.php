<?php
  $homePage = "home";
  $feedPage = "feed";
?>

<aside class="side-bar">
    <ul class="menu">
        <li class="menu__list <?php if($page === $feedPage):?>select<?php endif; ?>">
            <a href="http://mysyte.com/php/partials/feed.php" class="menu__link">
                <img class="menu__icon" src="../../assets/icons/home-icon.svg" alt="Домашняя страница" />
            </a>
        </li>
        <li class="menu__list <?php if($page === $homePage):?>select<?php endif; ?>">
            <a href="http://mysyte.com/php/partials/profile.php?id=1" class="menu__link">
                <img class="menu__icon" src="../../assets/icons/profile-icon.svg" alt="Страница пользователя" />
            </a>
        </li>
        <li class="menu__list">
            <a href="#" class="menu__link">
                <img class="menu__icon" src="../../assets/icons/plus-icon.svg" alt="Новый пост" />
            </a>
        </li>
    </ul>
</aside>