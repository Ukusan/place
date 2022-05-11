<?php
/* Smarty version 4.1.0, created on 2022-05-08 17:17:05
  from '/var/www/project/template/index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.1.0',
  'unifunc' => 'content_6277fb11193ae0_52983291',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '61c4fd9b9af887e8c232355ad919438e6dd29028' => 
    array (
      0 => '/var/www/project/template/index.tpl',
      1 => 1652030185,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6277fb11193ae0_52983291 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html>
<head>
    <meta charset=UTF-8>
    <title>Kosmos place</title>
    <style>
        
            body {display: flex;justify-content: center;}
            rect.selected {stroke: rgb(0,0,0);}
        
    </style>
</head>
<body>
    <svg width="1000" height="1000" id="panel">
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['pixels']->value, 'pixel');
$_smarty_tpl->tpl_vars['pixel']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['pixel']->value) {
$_smarty_tpl->tpl_vars['pixel']->do_else = false;
?>
            <rect class="panel-pixel"
                  data-x="<?php echo $_smarty_tpl->tpl_vars['pixel']->value->getX();?>
"
                  data-y="<?php echo $_smarty_tpl->tpl_vars['pixel']->value->getY();?>
"
                  x="<?php echo $_smarty_tpl->tpl_vars['pixel']->value->getX()*10;?>
"
                  y="<?php echo $_smarty_tpl->tpl_vars['pixel']->value->getY()*10;?>
"
                  width="10"
                  height="10"
                  fill="<?php echo $_smarty_tpl->tpl_vars['pixel']->value->getColor();?>
"
            />
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </svg>
    <form id="colorChanger">
        <input name="color" type="color"/>
        <input name="x" type="hidden"/>
        <input name="y" type="hidden"/>
        <input type="submit"/>
    </form>
    
        <?php echo '<script'; ?>
>
            (function () {
                function sendForm(event) {
                    event.preventDefault();
                    let form = event.target;
                    let formData = new FormData(form);

                    // console.log(JSON.stringify(Array.from(formData.entries())));
                    if(formData.has("color") && formData.has("x") && formData.has("y")){
                        fetch("/panel", {
                            method: "POST",
                            body: formData
                        }).then(res => {
                            console.log("Request complete! response:", res);
                        });
                    }

                    return false;
                }

                function selectPixel(event) {
                    console.log(event);
                    let previous = Array.from(document.getElementsByClassName('selected'));
                    previous.forEach(element => element.classList.remove('selected'));
                    event.target.classList.add("selected");
                    document.querySelector("input[name='x']").setAttribute("value",event.target.getAttribute('data-x'));
                    document.querySelector("input[name='y']").setAttribute("value",event.target.getAttribute('data-y'));
                    document.querySelector("input[name='color']").click();
                }

                function addListener(){
                    console.log("addListener");
                    let form = document.querySelector("#colorChanger");
                    form.addEventListener("submit",sendForm);

                    let panel = document.getElementById("panel");
                    panel.addEventListener('click', selectPixel)
                }

                function listenToUpdate(){
                    console.log("listenToUpdate");
                    let sse = new EventSource('/sse');
                    sse.addEventListener('pixel_updated',function(e){
                        let datum = JSON.parse(e.data);
                        console.table(datum);
                        datum.forEach(function(data){
                            let selectors = "rect[data-x='"+data.x+"'][data-y='"+data.y+"']";
                            let element = document.querySelector(selectors);
                            if(element !== null){
                                element.setAttribute("fill",data.color);
                            }
                        });

                    },false);
                }

                addListener();
                listenToUpdate();
            })();
        <?php echo '</script'; ?>
>
    
</body>
</html>
<?php }
}
