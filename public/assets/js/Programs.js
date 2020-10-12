class Programs {
    channels = $(".channel")
    selectedDataChannelValue = null

    /**
     * Inicializálás, evenlistenerek beállítása
     */
    init(){
        this.channels.each((i, channel)=>{
            channel.addEventListener("click", ()=>{
                this.setSelected(i)
                this.selectedDataChannelValue = $(channel).data("ch-name")
                this.getAvailableDays()
            })
        })

        $('body').on('change', '#days__select', (e)=>{
            this.getPrograms($("#days__select").val(), this.selectedDataChannelValue)
        });

        $("#start-query").click(()=>{
            this.reqQuery($("#date-picker").val())
        })
    }

    /**
     * Kiválasztott csatorna megjelölése
     */
    setSelected(i){
        this.channels.each((y, channel)=>{
            if(i == y){
                $(channel).addClass("selected")
            } else {
                $(channel).removeClass("selected")
            }
        })
    }

    /**
     * GET req a server felé.
     * Visszadja a már lekérdezett napokat és 
     * <select> formájában beszúrja DOM-ba őket.
     */
    getAvailableDays(){
        $.ajax({ 
            method: "GET",
            url: "public/api/getAvailableDays.php", 
            data: `channelName=${this.selectedDataChannelValue}`,
            success: (result)=>{
                $("#days-selector-box").empty()
                $("#days-selector-box").append('<select id="days__select" class="form-control"></select>')
                JSON.parse(result).map(data=>{
                    $("#days__select").append(`<option>${data.date}</option>`)
                })
                this.getPrograms($("#days__select").val(), this.selectedDataChannelValue)
            },
            error: (err)=>{
                console.log(err)
            },
            beforeSend: ()=>{
                $(this.daysSelect).append(`<div id="spinner"><div class="spinner-border"  role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div></div>`)
            },
            complete: ()=>{
                $("#spinner").remove();
            }
        });
    }

    /**
     * Nap és csatorna alapján lekérdezi a mentett programokat
     * GET request
     */
    getPrograms(day, cName){

        $.ajax({ 
            method: "GET",
            url: "public/api/getPrograms.php", 
            data: { channelName: cName, day: day },
            success: (result)=>{
                JSON.parse(result).map(data=>{
                    $("#programs").append(`<tr><td>${data.program_title}</td><td>${data.start_date}</td><td>${data.program_desc}</td><td>${data.age_limit}</td></tr>`)
                })
            },
            error: (err)=>{
                console.log(err)
            },
            beforeSend: ()=>{
                $("#programs").empty();
                $("#programs").append(`<div id="spinner"><div class="spinner-border"  role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div></div>`)
            },
            complete: ()=>{
                $("#spinner").remove();
            }

        });
    }

    /**
     * GET request
     * Nap alapján a szerver lekérdezi a port.hu-t
     */
    reqQuery(day){
        $.ajax({ 
            method: "GET",
            url: "public/api/getProgramsByDay.php", 
            data: `day=${day}`,
            success: (result)=>{
                $("#query-result").empty()
                let parsedResult = JSON.parse(result)
                if(parsedResult.success){
                    $("#query-result").append('<div class="success">Sikeres lekérdezés</div>')

                } else {
                    $("#query-result").append('<div class="error">Nincs adat a port.hu oldalon</div>')

                }
            },
            error: (err)=>{
                console.log(err)
                $("#query-result").empty()
                $("#query-result").append('<div class="error">Hiba a lekérdezés során</div>')
            },
            beforeSend: ()=>{
                $("#query-result").empty()
                $("#query-result").append(`<div id="spinner"><div class="spinner-border"  role="status">
                                            <span class="sr-only">Loading...</span>
                                         </div></div>`)
            },
            complete: ()=>{
                $("#spinner").remove();
            }
        });
    }

}


const program = new Programs;
program.init();
