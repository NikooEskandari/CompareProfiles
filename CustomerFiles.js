class CustomerFiles extends React.Component {
    constructor(props) {
        super(props);
        this.state = {};
    }

    render() {
        return (
            React.createElement('div', {
                    id: 'customer_files_list',
                }, React.createElement("table", {
                    id: 'customer_files_table',
                }, React.createElement("thead", null
                , React.createElement("tr", null
                    , React.createElement("th", null, "Name")
                    , React.createElement("th", null, "Customer Count")))
                , React.createElement("tbody", null))
            )
        )
    }
}

$(function () {
    $('#customer_files_table').DataTable({
        "data":
        customerFiles_fromServer,
        "columnDefs": [{
            "targets": [0],
            "data": "name",
        }, {
            "targets": [1],
            "data": "valueField"
        }],
        "responsive": true,
        "autoWidth": false,
        "sScrollXInner": "100%",
        "scrollCollapse": true,
        "Destroy": true,
        "scrollY": "30vh"
    });
});


