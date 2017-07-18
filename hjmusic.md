## 前言
所有的请求内容都是application/json，且所有请求都会附带Access-Token自定义头部用来让API服务器进行身份认证，未登录的用户Access-Token为空

## User
### 注册

#### HTTP Request
POST /users
#### 传入参数

| 参数名 | 必须 | 说明 |
|:--|:--|:--|
| email | true |用户邮箱 |
| password | true | 用户密码 |

#### 返回值
| 参数名 | 类型 | 说明 |
| :-- | :-- | :-- |
| id | int | 用户id |
| access_token | string | 身份认证令牌 |
| username | string | 用户昵称 |
| gender | string | 性别(候选值not_specified,male,female) |
| realname | string | 真实姓名 |
| email | string | 用户邮箱 |
| auth | string | 用户身份(候选值user, admin) |

#### 例子
```Json
 {
  "id": "1",
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE0OTcwMTA3NTAsImF1dGgiOiJ1bmRlcmdyYWR1YXRlIiwidXNlcl9pZCI6IjIifQ.FJOSGojP_gCIIveKDyARLDdPIJeSafPyc1UYtiVmjqk",
  "username": "lwio",
  "realname": "keke",
  "email": "443474713@qq.com",
  "auth": "admin"
}
```
------
### 登录

#### HTTP Request
POST /users/signin 对数据库进行身份验证，并将其重定向到新的会话或重定向到登录
#### 传入参数
|参数名|说明|
|:--|:--|
|user_email|用户邮箱|
|password|用户密码|
#### 返回值

|参数名|类型|说明|
|:--|:--|:--|
|token|string|加密信息|
|user_id|int|用户id|
|user_name|string|用户昵称|
|real_name|string|真实姓名|
|user_email|string|用户邮箱|
|auth|string|用户身份|

#### 例子
```
{
  "user_id": "2",
  "user_name": "",
  "real_name": "",
  "user_email": "1101010@qq.com0",
  "auth": "undergraduate",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE0OTcwMTA3NTAsImF1dGgiOiJ1bmRlcmdyYWR1YXRlIiwidXNlcl9pZCI6IjIifQ.FJOSGojP_gCIIveKDyARLDdPIJeSafPyc1UYtiVmjqk"
}
```
    
### 获取用户信息
GET /users 获取所有注册用户信息 (需要提供分页和filed两个功能)
#### 传入参数
可以提供offset和limit以及filed
```
{
    data: [
        {
            "id": 3,
            "username": "lwio",
            "realname": "keke",
            "email": "443474713@qq.com",
            "auth": "admin",
        },{
            "id": 4,
            "username": "lwio",
            "realname": "keke",
            "email": "443474713@qq.com",
            "auth": "admin",
        },
    ],
    paging: {
        first: "https://api.darlin.me/user/1/collections?offset=0&limit=5",
        previous: "https://api.darlin.me/user/1/collections?offset=5&limit=5",
        next: "https://api.darlin.me/user/1/collections?offset=10&limit=5",
        final: "https://api.darlin.me/user/1/collections?offset=35&limit=5",
    }
}
```

GET /users/:id 获取id用户信息
#### 传入参数
可以提供filed

#### 返回值

| 参数名 | 类型 | 说明 |
| :-- | :-- | :-- |
| id | int | 用户id |
| username | string | 用户昵称 |
| realname | string | 真实姓名 |
| email | string | 用户邮箱 |
| auth | string | 用户身份 |

#### 例子
```

  {
    "id": 3,
    "username": "lwio",
    "realname": "keke",
    "email": "443474713@qq.com",
    "auth": "admin",
  },
```

## Music
### 获取音乐情况
**注意，所有演唱作曲作词都是音乐人，作为一个类型的对象。**

GET /music

GET /music?offset=0?limit=5;

GET /music/:id
#### 传入参数
|参数名|说明|
|:--|:--|
|-|-|

#### 返回值
|参数名|类型|说明|
|:--|:--|:--|
|id|int|音乐id|
|name|string|歌名|
| cover_url | string | 封面图片路径 |
|singer|object|演唱者|
|composer|object|作曲者|
|lyricist|object|作词者|
| lyric_url | string | 歌词请求url |
| album | object | 所属专辑 |
| src_url | string | 音频资源路径 |
| published_at | string | UTC时间(2009-01-17T20:14:40Z) |
#### 例子
#####返回指定id的music信息
```
{
    id: '123',
    name: "miaomiao",
    coverr_url: "http://img4.duitang.com/uploads/item/201404/15/20140415093826_SzcNe.thumb.700_0.jpeg",
    lyric_url: "https://api.darlin.me/music/lyric/12/",
    src_url: "http://data.5sing.kgimg.com/G104/M09/1C/1D/qA0DAFk1fVGAGWkMAOMuQpygo8g155.mp3",
    published_at: "2009-01-17T20:14:40Z",
    singer: {
        id: '1',
        name: 'adyden'
    },
    composer: {
        id: '3',
        name: 'fuck'
    },
    lyricist: {
        id: '3',
        name: 'fuck',
    }
}
```

#####返回不带分页的所有music信息
```
[
    {
        id: '123',
        name: "miaomiao",
        coverr_url: "http://img4.duitang.com/uploads/item/201404/15/20140415093826_SzcNe.thumb.700_0.jpeg",
        lyric_url: "https://api.darlin.me/music/lyric/12/",
        src_url: "http://data.5sing.kgimg.com/G104/M09/1C/1D/qA0DAFk1fVGAGWkMAOMuQpygo8g155.mp3",
        published_at: "2009-01-17T20:14:40Z",
        singer: {
            id: '1',
            name: 'adyden'
        },
        composer: {
            id: '3',
            name: 'fuck'
        },
        lyricist: {
            id: '3',
            name: 'fuck',
        }
    },
    {
        id: '123',
        name: "miaomiao",
        coverr_url: "http://img4.duitang.com/uploads/item/201404/15/20140415093826_SzcNe.thumb.700_0.jpeg",
        lyric_url: "https://api.darlin.me/music/lyric/12/",
        src_url: "http://data.5sing.kgimg.com/G104/M09/1C/1D/qA0DAFk1fVGAGWkMAOMuQpygo8g155.mp3",
        published_at: "2009-01-17T20:14:40Z",
        singer: {
            id: '1',
            name: 'adyden'
        },
        composer: {
            id: '3',
            name: 'fuck'
        },
        lyricist: {
            id: '3',
            name: 'fuck',
        }
    },
        ……
]
```

#####返回带分页的music信息
```
{
    "data": [
        {
            id: '123',
            name: "miaomiao",
            coverr_url: "http://img4.duitang.com/uploads/item/201404/15/20140415093826_SzcNe.thumb.700_0.jpeg",
            lyric_url: "https://api.darlin.me/music/lyric/12/",
            src_url: "http://data.5sing.kgimg.com/G104/M09/1C/1D/qA0DAFk1fVGAGWkMAOMuQpygo8g155.mp3",
            published_at: "2009-01-17T20:14:40Z",
            singer: {
                id: '1',
                name: 'adyden'
            },
            composer: {
                id: '3',
                name: 'fuck'
            },
            lyricist: {
                id: '3',
                name: 'fuck',
            }
        ｝,
        {
            id: '123',
            name: "miaomiao",
            coverr_url: "http://img4.duitang.com/uploads/item/201404/15/20140415093826_SzcNe.thumb.700_0.jpeg",
            lyric_url: "https://api.darlin.me/music/lyric/12/",
            src_url: "http://data.5sing.kgimg.com/G104/M09/1C/1D/qA0DAFk1fVGAGWkMAOMuQpygo8g155.mp3",
            published_at: "2009-01-17T20:14:40Z",
            singer: {
                id: '1',
                name: 'adyden'
            },
            composer: {
                id: '3',
                name: 'fuck'
            },
            lyricist: {
                id: '3',
                name: 'fuck',
            }
        }
    ],
    "paging": {
        "previous": "localhost/music?offset=1&limit=2",
        "next": "localhost/music?offset=3&limit=2"
    }
}
```

### 上传新音乐
**注意，这个需要验证管理员权限**   
**此功能提供两API，分别负责上传文件和上传文件信息**

上传文件   
POST /upload/music
#### 传入参数
通过post数据传输key值为music_file的音乐文件

#### 返回值
带文件后缀的加密令牌
```
2a68c6c1532f4bc81bf58fe68bad8e532738b17880f6f2ad635ab8d1ba9c9a8d.mp3
```    
         
上传信息  
POST /music
#### 传入参数
**同时，上传音乐信息时，需要将从第一个api中返回的数据，保存在key值为File-Token的自定义头部中**  
|参数名| 类型 | 必须 | 默认 | 说明|
|:--|:--|:--|:--|:--|
|name|string|true|-|歌名|
| cover_url | string | false | 默认图片url | 封面图片路径 |
|singer|object|true|-|演唱者|
|composer_id|int|false| null |作曲者|
|lyricist_id|int|false| null |作词者|
| lyric_url | string |false| null |歌词请求url |
| album | object | false | null |所属专辑 |
| src_url | string | true | - | 音频资源路径 |
| published_at | string | true | - |  UTC时间(2009-01-17T20:14:40Z) 

#### 返回值
|参数名|类型|说明|
|:--|:--|:--|
|id|int|音乐id|
|name|string|歌名|
| cover_url | string | 封面图片路径 |
|singer|object|演唱者|
|composer|object|作曲者|
|lyricist|object|作词者|
| lyric_url | string | 歌词请求url |
| album | object | 所属专辑 |
| src_url | string | 音频资源路径 |
| published_at | string | UTC时间(2009-01-17T20:14:40Z) |
#### 例子
```
{
    id: '123',
    name: "miaomiao",
    coverr_url: "http://img4.duitang.com/uploads/item/201404/15/20140415093826_SzcNe.thumb.700_0.jpeg",
    lyric_url: "https://api.darlin.me/music/lyric/12/",
    src_url: "http://data.5sing.kgimg.com/G104/M09/1C/1D/qA0DAFk1fVGAGWkMAOMuQpygo8g155.mp3",
    published_at: "2009-01-17T20:14:40Z",
    singer: {
        id: '1',
        name: 'adyden'
    },
    composer: {
        id: '3',
        name: 'fuck'
    },
    lyricist: {
        id: '3',
        name: 'fuck',
    }
｝
```

### 修改歌曲信息
**注意，这个需要验证管理员权限**

PUT /music/:id
#### 传入参数
|参数名|类型|必须|默认|说明|
|:--|:--|:--|:--|:--|
|name|string|false|null|歌名|
| cover_url | string | false | null | 封面图片路径 |
|singer|object|false|null|演唱者|
|composer_id|int|false| null |作曲者|
|lyricist_id|int|false| null |作词者|
| lyric_url | string |false| null |歌词请求url |
| album | object | false | null |所属专辑 |
| src_url | string |false | null | 音频资源路径 |
| published_at | string |false| null |  UTC时间(2009-01-17T20:14:40Z) |

#### 返回值
|参数名|类型|说明|
|:--|:--|:--|
|id|int|音乐id|
|name|string|歌名|
| cover_url | string | 封面图片路径 |
|singer|object|演唱者|
|composer|object|作曲者|
|lyricist|object|作词者|
| lyric_url | string | 歌词请求url |
| album | object | 所属专辑 |
| src_url | string | 音频资源路径 |
| published_at | string | UTC时间(2009-01-17T20:14:40Z) |

#### 例子
```
{
    id: '123',
    name: "miaomiao",
    coverr_url: "http://img4.duitang.com/uploads/item/201404/15/20140415093826_SzcNe.thumb.700_0.jpeg",
    lyric_url: "https://api.darlin.me/music/lyric/12/",
    src_url: "http://data.5sing.kgimg.com/G104/M09/1C/1D/qA0DAFk1fVGAGWkMAOMuQpygo8g155.mp3",
    published_at: "2009-01-17T20:14:40Z",
    singer: {
        id: '1',
        name: 'adyden'
    },
    composer: {
        id: '3',
        name: 'fuck'
    },
    lyricist: {
        id: '3',
        name: 'fuck',
    }
}
```

### 删除歌曲信息
**注意，这个需要管理员权限**

DELETE /music/:id
#### 传入参数
|参数名|说明|
|:--|:--|
|-|-|

#### 返回HTTP响应状态
* 成功
  * 删除成功：204
                
* 失败：
  * 歌曲(id)不存在：404  
  * 用户权限不够：403  


## 收藏

### 获取某用户的收藏（需要提供分页）

GET /users/:id/collections
#### 传入参数
|参数名|说明|
|--|--|
|-|-|

#### 返回值
|参数名|类型|说明|
|:--|:--|:--|
|id|int|收藏id|
|collect_at|string|收藏事件UTC|
|music|object|music对象|

#### 例子
```
{
    data: [
        {
            id: 12,
            collect_at: "published_at: "2009-01-17T20:14:40Z",
            music: {
                id: '123',
                name: "miaomiao",
                coverr_url: "http://img4.duitang.com/uploads/item/201404/15/20140415093826_SzcNe.thumb.700_0.jpeg",
                singer: {
                    id: '1',
                    name: 'adyden'
                },
                composer: {
                    id: '3',
                    name: 'fuck'
                },
                lyricist: {
                    id: '3',
                    name: 'fuck',
                },
                lyric_url: "https://api.darlin.me/music/lyric/12/",
                album: {
                    id: '5',
                    name: 'album test',
                    cover_url: "",
                    songs_num: 12,
                },
                src: "http://data.5sing.kgimg.com/G104/M09/1C/1D/qA0DAFk1fVGAGWkMAOMuQpygo8g155.mp3",
                published_at: "2009-01-17T20:14:40Z",    
            }
        }, {
            id: 12,
            collect_at: "published_at: "2009-01-17T20:14:40Z",
            music: {
                id: '123',
                name: "miaomiao",
                coverr_url: "http://img4.duitang.com/uploads/item/201404/15/20140415093826_SzcNe.thumb.700_0.jpeg",
                singer: {
                    id: '1',
                    name: 'adyden'
                },
                composer: {
                    id: '3',
                    name: 'fuck'
                },
                lyricist: {
                    id: '3',
                    name: 'fuck',
                },
                lyric_url: "https://api.darlin.me/music/lyric/12/",
                album: {
                    id: '5',
                    name: 'album test',
                    cover_url: "",
                    songs_num: 12,
                },
                src: "http://data.5sing.kgimg.com/G104/M09/1C/1D/qA0DAFk1fVGAGWkMAOMuQpygo8g155.mp3",
                published_at: "2009-01-17T20:14:40Z",    
            }
        }
    ],
    paging: {
        first: "https://api.darlin.me/user/1/collections?offset=0&limit=5",
        previous: "https://api.darlin.me/user/1/collections?offset=5&limit=5",
        next: "https://api.darlin.me/user/1/collections?offset=10&limit=5",
        final: "https://api.darlin.me/user/1/collections?offset=35&limit=5",
    }
}
```

### 用户添加歌曲到收藏

POST /music/:id/collect
#### 传入参数
|参数名|类型|说明|
|--|--|--|
|-|-|-|

#### 返回HTTP响应状态
* 成功
  * 收藏成功：204  

* 失败
  * 歌曲不存在：404  
  * 歌曲已收藏：409  

## 歌词
### 获取歌词列表
**需要提供分页和filed以及是否审核三个功能**
GET /lyrics
#### 传入参数
|参数名|必须|说明|
|:--|:--|:--|
|-|-|-|

#### 返回值
| 参数名 | 类型 | 说明 |
|:--:|:--:|:--:|
| id | int | 歌词id |
| music | object | 歌曲名 |
| uploader | object | 上传者 |
| uploaded_at | datetime | 上传时间 |
| lyric | text | 歌词内容 |
| check | enum | 是否审核 |
#### 例子
```
{
    data: [
        {
            id: 12,
            uploaded_at: "2009-01-17T20:14:40Z",
            lyric: "afeihoIFIOEHOIefooqihfoIHFOoifoehqofhqoiefoihqfoOHOIqefofhoqfehoqfhoqe",
            music: {
                id: '123',
                name: "miaomiao",
                coverr_url: "http://img4.duitang.com/uploads/item/201404/15/20140415093826_SzcNe.thumb.700_0.jpeg",
                singer: {
                    id: '1',
                    name: 'adyden'
                },
                composer: {
                    id: '3',
                    name: 'fuck'
                },
                lyricist: {
                    id: '3',
                    name: 'fuck',
                },
                lyric_url: "https://api.darlin.me/music/lyric/12/",
                album: {
                    id: '5',
                    name: 'album test',
                    cover_url: "",
                    songs_num: 12,
                },
                src: "http://data.5sing.kgimg.com/G104/M09/1C/1D/qA0DAFk1fVGAGWkMAOMuQpygo8g155.mp3",
                published_at: "2009-01-17T20:14:40Z",    
            },
            uploader:  {
                "id": 3,
                "username": "lwio",
                "realname": "keke",
                "email": "443474713@qq.com",
                "auth": "admin",
            },
            check: '1',
        }
    ],
    paging: {
        first: "https://api.darlin.me/user/1/collections?offset=0&limit=5",
        previous: "https://api.darlin.me/user/1/collections?offset=5&limit=5",
        next: "https://api.darlin.me/user/1/collections?offset=10&limit=5",
        final: "https://api.darlin.me/user/1/collections?offset=35&limit=5",
    }
}
```
---
 
### 获取歌词对象信息
GET /lyrics/:id    
#### 传入参数
|参数名|类型|说明|
|:--|:--|:--|
|-|-|-|


#### 返回值
|参数名|类型|说明|
|:--:|:--:|:--:|
|id|int|歌词id|
|music|object|歌曲|
|uploader|object|上传者|
|uploaded_at|datetime|上传时间|
|lyric|text|歌词内容|
#### 例子
```
{
    id: 12,
    uploaded_at: "2009-01-17T20:14:40Z",
    lyric: "afeihoIFIOEHOIefooqihfoIHFOoifoehqofhqoiefoihqfoOHOIqefofhoqfehoqfhoqe",
    music: {
        id: '123',
        name: "miaomiao",
        coverr_url: "http://img4.duitang.com/uploads/item/201404/15/20140415093826_SzcNe.thumb.700_0.jpeg",
        singer: {
            id: '1',
            name: 'adyden'
        },
        composer: {
            id: '3',
            name: 'fuck'
        },
        lyricist: {
            id: '3',
            name: 'fuck',
        },
        lyric_url: "https://api.darlin.me/music/lyric/12/",
        album: {
            id: '5',
            name: 'album test',
            cover_url: "",
            songs_num: 12,
        },
        src: "http://data.5sing.kgimg.com/G104/M09/1C/1D/qA0DAFk1fVGAGWkMAOMuQpygo8g155.mp3",
        published_at: "2009-01-17T20:14:40Z",    
    },
    uploader:  {
        id: 3,
        username: "lwio",
        realname: "keke",
        email: "443474713@qq.com",
        auth: "admin",
    },
    check: '1',
}
```
-----

### 上传歌词
POST /lyrics     
#### 传入参数
|参数名|类型|必须|默认|说明|
|:--:|:--:|:--:|:--:|:--:|
|music_id|int|true|-|歌曲名|
|lyric|text|true|-|歌词内容|

#### 返回值
|参数名|类型|说明|
|:--:|:--:|:--:|
|id|int|歌词id|
|music|object|歌曲名|
|uploader|object|上传者|
|uploaded_at|string|上传时间|
|lyric|text|歌词内容|
|check|enum|是否审核|
#### 例子
```
{
    id: 12,
    uploaded_at: "published_at: "2009-01-17T20:14:40Z",
    lyric: "afeihoIFIOEHOIefooqihfoIHFOoifoehqofhqoiefoihqfoOHOIqefofhoqfehoqfhoqe",
    music: {
        id: '123',
        name: "miaomiao",
        coverr_url: "http://img4.duitang.com/uploads/item/201404/15/20140415093826_SzcNe.thumb.700_0.jpeg",
        singer: {
            id: '1',
            name: 'adyden'
        },
        composer: {
            id: '3',
            name: 'fuck'
        },
        lyricist: {
            id: '3',
            name: 'fuck',
        },
        lyric_url: "https://api.darlin.me/music/lyric/12/",
        album: {
            id: '5',
            name: 'album test',
            cover_url: "",
            songs_num: 12,
        },
        src: "http://data.5sing.kgimg.com/G104/M09/1C/1D/qA0DAFk1fVGAGWkMAOMuQpygo8g155.mp3",
        published_at: "2009-01-17T20:14:40Z",    
    },
    uploader:  {
        id: 3,
        username: "lwio",
        realname: "keke",
        email: "443474713@qq.com",
        auth: "admin",
    },
    check: '0',
}
```

-----

### 删除歌词
**需要管理员权限**
DELETE /lyrics/:id 
#### 传入参数
|参数名|类型|说明|
|:--|:--|:--|
|-|-|-|


#### 返回HTTP响应状态
* 成功
  * 删除成功：204
                
* 失败：
  * 歌词(id)不存在：404  
  * 用户权限不够：403  

-----


---



POST /lyrics/:id/approve  审核歌词(管理员权限) (下个版本)

---

POST /music/:id/review      用户评论歌曲 (下个版本)

---
