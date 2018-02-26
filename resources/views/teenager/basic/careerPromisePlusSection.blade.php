<div class="promise-plus front_page">
    <div class="heading">
        <span><i class="icon-plus"></i></span>
        <h3>Promise Plus</h3>
    </div>
    <p>Your individualized suggestion based on your career explore role play tasks. The more you explore, the more the suggestion gets refined</p>
    <div class="unbox-btn"><a href="#" title="Unbox Me" class="btn-primary" data-toggle="modal" data-target="#myModal"><span class="unbox-me">Unbox Me</span><span class="coins-outer"><span class="coins"></span> 25000</span></a></div>
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content custom-modal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                    <h4 class="modal-title">Congratulations!</h4>
                </div>
                <div class="modal-body">
                    <p>You have 42,000 ProCoins available.</p>
                    <p>Click OK to consume your 250 ProCoins and play on</p>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-primary btn-next" data-dismiss="modal" onclick="getPromisePlusData({{$professionsData->id}});">ok</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>
</div>
<div id="showPromisePlusData">
    
</div>
