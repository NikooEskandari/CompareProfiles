class CompareProfiles extends React.Component {
    constructor(props) {
        super(props);
        this.activeTab = "CustomerFiles_li";
        this.handleClicked = this.handleClicked.bind(this);
    }

    handleClicked(activeLi) {
        this.activeTab = activeLi;
        GetActiveTabContent({activeTab: activeLi});
    }

    render() {
        const activeTab = this.activeTab;

        return (
            React.createElement("div", {
                id: "compare_profiles_div"
            }, React.createElement("div", {
                    className: "Tabs"
                }, React.createElement("ul", {
                    className: "compare_profiles_nav"
                }, React.createElement("li", {
                    className: activeTab === "CustomerFiles_li" ? "active" : "",
                    onClick: () => this.handleClicked("CustomerFiles_li")
                }, "Customer Files")
                , React.createElement("li", {
                    className: activeTab === "TradeareaFiles_li" ? "active" : "",
                    onClick: () => this.handleClicked("TradeareaFiles_li")
                }, "Trade Area Files")
                , React.createElement("li", {
                    className: activeTab === "TradeareaMaps_li" ? "active" : "",
                    onClick: () => this.handleClicked("TradeareaMaps_li")
                }, "Trade Area Maps")
                , React.createElement("li", {
                    className: activeTab === "PredefinedMarkets_li" ? "active" : "",
                    onClick: () => this.handleClicked("PredefinedMarkets_li")
                }, "Predefined Markets"))
                , React.createElement(GetActiveTabContent, {activeTab: activeTab})))
        );
    }
}

function GetActiveTabContent(param) {
    if (typeof param.activeTab.replace === 'undefined') {
        return null;
    }

    let relatedClass = param.activeTab.replace("_li", "");
    let relatedContent = '';

    switch (relatedClass) {
        case "CustomerFiles":
            relatedContent = React.createElement(CustomerFiles, {});
            break;
    }

    return React.createElement('div', {
        className: 'compare_profiles_tab_contents',
    }, relatedContent)

}

ReactDOM.render(React.createElement(CompareProfiles, {}), document.getElementById("root"));
