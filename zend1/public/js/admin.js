function menu_buttons(){
		$('button[act="them"]').button({
            icons: {
                primary: "ui-icon-circle-plus"
            }
        });
		$('button[act="them_con"]').button({
            icons: {
                primary: "ui-icon-circle-plus"
            }
        });
		$('button[act="sua"]').button({
            icons: {
                primary: "ui-icon-circle-minus"
            }
        });
		$('button[act="sua_chitiet"]').button({
            icons: {
                primary: "ui-icon-circle-minus"
            }
        });
		$('button[act="chuyennhom"]').button({
            icons: {
                primary: "ui-icon-circle-minus"
            }
        });
		$('button[act="xoa"]').button({
            icons: {
                primary: "ui-icon-circle-close"
            }
		});
		$('button[act="commit"]').button({
			icons: {
				primary: "ui-icon-refresh"
			}
		});
		$('button[act="rollback"]').button({
			icons: {
				primary: "ui-icon-arrowreturn-1-w"
			}
		});
		$('button[act="run"]').button({
			icons: {
				primary: "ui-icon-gear"
			}
		});
		$('button[act="exp"]').button({
			icons: {
				primary: "ui-icon-print"
			}
		});
		$('button[act="imp"]').button({
			icons: {
				primary: "ui-icon-print"
			}
		});
		$('button[act="xuat"]').button({
			icons: {
				primary: "ui-icon-print"
			}
		});
}