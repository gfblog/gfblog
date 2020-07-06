var Towxml = require('/assets/libs/towxml/main.js');
App({
  //请将apiUrl换成你的地址,注意/addons/blog/wxapp.不能修改
  apiUrl: 'https://demo.fastadmin.net/addons/blog/wxapp.',
  si: 0,
  //小程序启动
  onLaunch: function () {
    var that = this;
    that.request('/common/init', {}, function (data, ret) {
      that.globalData.config = data.config;
      that.globalData.tabList = data.tabList;
      //如果需要一进入小程序就要求授权登录,可在这里发起调用
      //that.check(function (ret) { });
    }, function (data, ret) {
      that.error(ret.msg);
    });
  },
  //判断是否登录
  check: function (cb) {
    var that = this;
    if (this.globalData.userInfo) {
      typeof cb == "function" && cb(this.globalData.userInfo);
    } else {
      this.login(cb);
    }
  },
  //登录
  login: function (cb) {
    var that = this;
    var token = wx.getStorageSync('token') || '';
    //调用登录接口
    wx.login({
      success: function (res) {
        if (res.code) {
          //发起网络请求
          wx.getUserInfo({
            success: function (ures) {
              console.log(ures);
              that.globalData.userInfo = ures.userInfo;
              typeof cb == "function" && cb(that.globalData.userInfo);
            },
            fail: function (res) {
              that.showLoginModal(cb);
            }
          });
        } else {
          that.showLoginModal(cb);
        }
      }
    });
  },
  //显示登录或授权提示
  showLoginModal: function (cb) {
    var that = this;
    if (!that.globalData.userInfo) {
      //获取用户信息
      wx.getSetting({
        success: function (sres) {
          if (sres.authSetting['scope.userInfo']) {
            wx.showModal({
              title: '温馨提示',
              content: '当前无法获取到你的个人信息，部分操作可能受到限制',
              confirmText: "重新登录",
              cancelText: "暂不登录",
              success: function (res) {
                if (res.confirm) {
                  that.login(cb);
                } else {
                  console.log('用户暂不登录');
                }
              }
            });
          } else {
            wx.showModal({
              title: '温馨提示',
              content: '当前无法获取到你的个人信息，部分操作可能受到限制',
              confirmText: "去授权",
              cancelText: "暂不授权",
              success: function (res) {
                if (res.confirm) {
                  wx.openSetting({
                    success: function (sres) {
                      that.check(cb);
                    }
                  });
                } else {
                  console.log('用户暂不授权');
                }
              }
            });
          }
        }
      });
    } else {
      typeof cb == "function" && cb(that.globalData.userInfo);
    }
  },
  //发起网络请求
  request: function (url, data, success, error) {
    var that = this;
    if (typeof data == 'function') {
      success = data;
      error = success;
      data = {};
    }
    //移除最前的/
    while (url.charAt(0) === '/')
      url = url.slice(1);
    this.loading(true);
    let cookie = wx.getStorageSync('cookieKey');
    let header = {
      "Content-Type": "application/x-www-form-urlencoded"
    };
    if (cookie) {
      header.Cookie = cookie;
    }
    if (this.globalData.__token__) {
      data.__token__ = this.globalData.__token__;
    }
    data._ajax = 1;
    wx.request({
      url: this.apiUrl + url,
      data: data,
      method: 'post',
      header: header,
      success: function (res) {
        that.loading(false);
        var code, msg, json;
        if (res && res.header) {
          if (res.header['Set-Cookie']) {
            wx.setStorageSync('cookieKey', res.header['Set-Cookie']); //保存Cookie到Storage
          }
          if (res.header['__token__']) {
            that.globalData.__token__ = res.header['__token__'];
          }
        }
        if (res.statusCode === 200) {
          json = res.data;
          if (json.code === 1) {
            typeof success === 'function' && success(json.data, json);
          } else {
            typeof error === 'function' && error(json.data, json);
          }
        } else {
          json = typeof res.data === 'object' ? res.data : { code: 0, msg: '发生一个未知错误', data: null };
          typeof error === 'function' && error(json.data, json);
        }
      },
      fail: function (res) {
        that.loading(false);
        console.log("fail:", res);
        typeof error === 'function' && error(null, { code: 0, msg: '', data: null });
      }
    });
  },
  //文本提示
  info: function (msg, cb) {
    wx.showToast({
      title: msg,
      icon: 'none',
      duration: 2000,
      complete: function () {
        typeof cb == "function" && cb();
      }
    });
  },
  //成功提示
  success: function (msg, cb) {
    wx.showToast({
      title: msg,
      icon: 'success',
      image: '/assets/images/ok.png',
      duration: 2000,
      complete: function () {
        typeof cb == "function" && cb();
      }
    });
  },
  //错误提示
  error: function (msg, cb) {
    wx.showToast({
      title: msg,
      image: '/assets/images/error.png',
      duration: 2000,
      complete: function () {
        typeof cb == "function" && cb();
      }
    });
  },
  //警告提示
  warning: function (msg, cb) {
    wx.showToast({
      title: msg,
      image: '/assets/images/warning.png',
      duration: 2000,
      complete: function () {
        typeof cb == "function" && cb();
      }
    });
  },
  //Loading
  loading: function (msg) {
    if (typeof msg == 'boolean') {
      if (!msg) {
        if (!this.si) {
          return;
        }
        clearTimeout(this.si);
        wx.hideLoading({});
        return;
      }
    }
    msg = typeof msg == 'undefined' || typeof msg == 'boolean' ? '加载中' : msg;
    this.globalData.loading = true;
    if (this.si) {
      return;
    }
    this.si = setTimeout(function () {
      wx.showLoading({
        title: msg
      });
    }, 300);

  },
  towxml: new Towxml(),
  //全局信息
  globalData: {
    userInfo: null,
    config: null,
    __token__: null,
    tabList: [],
  }
})
