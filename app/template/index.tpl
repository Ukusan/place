<!DOCTYPE html>
<html>
<head>
    <meta charset=UTF-8>
    <title>Kosmos place</title>
    <style>
        {literal}
            body {display: flex;justify-content: center;}
            rect.selected {stroke: rgb(0,0,0);}
        {/literal}
    </style>
</head>
<body>
    <svg width="1000" height="1000" id="panel">
    {foreach $pixels as $pixel}
            <rect class="panel-pixel"
                  data-x="{$pixel->getX()}"
                  data-y="{$pixel->getY()}"
                  x="{$pixel->getX() * 10}"
                  y="{$pixel->getY() * 10}"
                  width="10"
                  height="10"
                  fill="{$pixel->getColor()}"
            />
    {/foreach}
    </svg>
    <form id="colorChanger">
        <input name="color" type="color"/>
        <input name="x" type="hidden"/>
        <input name="y" type="hidden"/>
        <input type="submit"/>
    </form>
    {literal}
        <script>
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
        </script>
    {/literal}
</body>
</html>
