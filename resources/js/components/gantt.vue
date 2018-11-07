<template>
    <div class="gantt-wrapper">
        <div class="list">
            <ul id="v-tasks-list" class="list-unstyled text-left">
                <li style="height: 45px; border-bottom: transparent;">
                    <button class="btn btn-outline-info float-right btn-pin" v-on:click="pinTaskList"><i
                        class="fa fa-unlock"></i></button>
                </li>
                <li style="height: 45px;"></li>
                <li v-for="(value, key) in tasks">{{value.name}}</li>
            </ul>
        </div>
        <div class="segments">
            <ul id="week-list">
            </ul>
            <ul id="days-list">
            </ul>
            <ul id="segments-list" class="list-unstyled">
                <gantt-segment v-for="(task, key) in tasks" :task="task" :key="key"></gantt-segment>
            </ul>
        </div>

        <div class="tooltip-wrapper">
            <span class="fa fa-pencil" @click="editTask"></span>
            <span class="fa fa-trash" @click="deleteTask"></span>
        </div>
        <edit-task-modal :task_obj="this.task_obj" v-on:success_callback="loadTasks()"></edit-task-modal>
    </div>
</template>

<script>
    import interact from 'interactjs'
    import editTaskModal from './edit-task-modal'
    import ganttSegment from './gantt-segment'

    let start_date;
    export default {
        name: 'gantt',
        data() {
            return {
                tasks: [],
                days: [],
                start_date: '',
                due_date: '',
                oneDay: 24 * 60 * 60 * 1000, // hours*minutes*seconds*milliseconds
                task_obj: {},
                weekday: [
                    "Sun",
                    "Mon",
                    "Tue",
                    "Wed",
                    "Thu",
                    "Fri",
                    "Sat"
                ],
                months: [
                    "Jan",
                    "Feb",
                    "Mar",
                    "Apr",
                    "May",
                    "Jun",
                    "Jul",
                    "Aug",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dic"
                ]
            }
        },
        mounted() {
            console.log('Component mounted. gantt');
            this.loadTasks();
        },
        methods: {
            loadTasks() {
                console.log('load tasks');
                const vue_scope = this;
                axios.get(window.location.origin + '/api/tasks/gantt').then(({data}) => {
                    vue_scope.tasks = [];
                    data.tasks.forEach(task => {
                        vue_scope.tasks.push(task);
                    });
                    vue_scope.days = [];
                    vue_scope.start_date = new Date(data.start_date);
                    vue_scope.due_date = new Date(data.due_date);

                    start_date = vue_scope.start_date;

                    let day_container = $("#days-list");
                    let week_container = $("#week-list");
                    let day_start = 31;
                    let day_end = 0;
                    let days_count = 0;

                    for (let d = new Date(vue_scope.start_date.valueOf());
                         d <= new Date(vue_scope.due_date.valueOf() + vue_scope.oneDay * 2);
                         d.setDate(d.getDate() + 1)) {
                        let current_day_calendar = d.getDate();
                        let day_week_number = d.getDay();
                        days_count++;
                        day_container.append('<li  data-toggle="tooltip" title="'
                            + current_day_calendar + '-' + (d.getMonth() + 1) + '-' + d.getFullYear() + '">'
                            + vue_scope.weekday[day_week_number] + '</li>');
                        if (current_day_calendar < day_start)
                            day_start = current_day_calendar;

                        if (current_day_calendar > day_end)
                            day_end = current_day_calendar;

                        if (day_week_number === 6 || vue_scope.isLastDay(d)) {
                            week_container.append('<li style="width: calc(45px * ' + (days_count) + ')">'
                                + vue_scope.months[d.getMonth()] + ' ' + day_start + ' - ' + day_end + '</li>');
                            day_start = 31;
                            day_end = 0;
                            days_count = 0;
                        } else if (d == new Date(vue_scope.due_date.valueOf() + vue_scope.oneDay * 2)) {
                            console.log('errorororororro');
                        }
                    }
                    $('[data-toggle="tooltip"]').tooltip()

                }).then(() => {
                    this.orderTasks();
                });
            },
            orderTasks() {
                console.log('order tasks ');
                const vue_scope = this;
                $("#segments-list li").each(function (index, item) {
                    const task_start_date = new Date($(item).data('start_date'));
                    const task_due_date = new Date($(item).data('due_date'));
                    const left_margin = (vue_scope.datediff(vue_scope.start_date, task_start_date) + 1) * 45;
                    const width = (vue_scope.datediff(task_start_date, task_due_date) + 1) * 45;

                    let task = $(item).find('.task');

                    task.attr('data-x', left_margin);
                    task.css({"transform": "translate(" + left_margin + "px, 0px)"});
                    task.css('width', width + 'px');
                });
                this.addScrollListener();
            },
            datediff(firstDate, secondDate) {

                return Math.round(Math.abs((firstDate.getTime() - secondDate.getTime()) / (this.oneDay)));
            },
            editTask(e) {

                let task = $(e.target).parent().parent();

                this.task_obj = {
                    'id': task.data('id'),
                    'name': task.data('name'),
                    'description': task.data('description'),
                    'start_date': task.data('start_date').substring(0, 10),
                    'due_date': task.data('due_date').substring(0, 10),
                };

                $("#editTaskModal").modal('show');
            },
            deleteTask() {
                alert('Delete task');
            },
            getDate(value) {
                console.log('date ' + value);
            },
            cleanErrorsForm() {
                $("form label.error").each(function (index, item) {
                    item.remove();
                })
            },
            isLastDay(dt) {
                let test = new Date(dt.getTime()), month = test.getMonth();
                test.setDate(test.getDate() + 1);
                return test.getMonth() !== month;
            },
            pinTaskList(event) {
                $(".gantt-wrapper").toggleClass('fixed');

                if ($(".gantt-wrapper").hasClass('fixed')) {
                    $(".gantt-wrapper").find('.btn-pin i').removeClass('fa-unlock').addClass('fa-lock');
                } else {
                    $(".gantt-wrapper").find('.btn-pin i').removeClass('fa-lock').addClass('fa-unlock');
                    $(".list").css('top', 0);
                }
            },
            addScrollListener() {
                let vue_scope = this;
                $('body').on('appear', '#week-list', function (event, $all_appeared_elements) {
                    // this element is now inside browser viewport
                    console.log('appear');
                    vue_scope.daysListAppear();
                });
                $('body').on('disappear', '#week-list', function (event, $all_disappeared_elements) {
                    // this element is now outside browser viewport
                    console.log('disappear');
                    vue_scope.daysListDisappear();
                });

                $("#week-list").appear();


                console.log('scroll');
            },
            handleScrollBody(event) {
                if ($(".gantt-wrapper").hasClass('fixed')) {
                    $(".list").css('top', -window.pageYOffset + 38);
                }
                if ($('#week-list-clone').length > 0) {
                    let left = parseInt(-window.scrollX) + parseInt($('.list').css('width'));
                    $('#week-list-clone').css('left', left);
                    $('#days-list-clone').css('left', left);
                }
            },
            daysListDisappear() {

                let left = parseInt(-window.scrollX) + parseInt($('.list').css('width'));
                $("#week-list")
                    .clone()
                    .attr('id', 'week-list-clone')
                    .css('position', 'fixed')
                    .css('left', left)
                    .appendTo('body');
                $("#days-list")
                    .clone()
                    .attr('id', 'days-list-clone')
                    .css('position', 'fixed')
                    .css('left', left)
                    .appendTo('body');
            },
            daysListAppear() {
                $("#week-list-clone").remove();
                $("#days-list-clone").remove();
            }
        },
        components: {
            'edit-task-modal': editTaskModal,
            'gantt-segment': ganttSegment,
        },
        created() {
            window.addEventListener('scroll', this.handleScrollBody);

        },
        destroyed() {
            this.daysListAppear();
        }
    }

    $(function () {
        interact('.resize-drag')
            .draggable({
                onmove: window.dragMoveListener,
                restrict: {
                    restriction: 'parent',
                    elementRect: {top: 0, left: 0, bottom: 1, right: 1}
                },
            })
            .resizable({
                // resize from all edges and corners
                edges: {left: true, right: true, bottom: false, top: false},

                // keep the edges inside the parent
                restrictEdges: {
                    outer: 'parent',
                    endOnly: true,
                },

                // minimum size
                restrictSize: {
                    min: {width: 45, height: 50},
                },

                inertia: true,
            })
            .on('resizemove', function (event) {
                var target = event.target,
                    x = (parseFloat(target.getAttribute('data-x')) || 0),
                    y = (parseFloat(target.getAttribute('data-y')) || 0);

                if (y != 0)
                    return;

                // update the element's style
                target.style.width = event.rect.width + 'px';
                target.style.height = event.rect.height + 'px';

                // translate when resizing from top or left edges
                x += event.deltaRect.left;
                y += event.deltaRect.top;

                target.style.webkitTransform = target.style.transform =
                    'translate(' + x + 'px,' + y + 'px)';

                target.setAttribute('data-x', x);
                target.setAttribute('data-y', y);
                target.textContent = Math.round(event.rect.width) + '\u00D7' + Math.round(event.rect.height);
                // console.log('movement ' + x);
            })
            .on('resizeend', function (event) {

                let target = event.target;
                let x = $(target).data('x');
                x = Math.round(x / 45);
                target.style.webkitTransform = target.style.transform = 'translate(' + (x * 45) + 'px, 0px)';

                target.setAttribute('data-x', x * 45);

                let width = $(target).css('width');
                width = width.replace('px', '');
                width = Math.round(width / 45);
                $(target).css('width', width * 45);

                let task_obj = {};
                task_obj.id = $(target).data('id');
                task_obj.name = $(target).data('name');
                task_obj.start_date = (new Date(start_date.getTime())).addDays(Math.round(x) - 1);
                task_obj.due_date = (new Date(task_obj.start_date.getTime())).addDays(Math.round(width) - 1);

                task_obj.start_date = (task_obj.start_date.toISOString()).substring(0, 10);
                task_obj.due_date = (task_obj.due_date.toISOString()).substring(0, 10);

                $.ajax({
                    url: window.location.origin + '/api/task',
                    type: 'post',
                    data: task_obj,
                    success: function (data) {
                        Vue.notify({
                            group: 'notification-template',
                            title: 'Success',
                            text: 'Task updated!',
                            type: 'success',
                            duration: '3500'
                        });
                        $(target).attr('data-start_date', task_obj.start_date);
                        $(target).attr('data-due_date', task_obj.due_date);
                    },
                    error: function (error) {
                        let errors = $.parseJSON(error.responseText);
                        Vue.notify({
                            group: 'notification-template',
                            title: 'Error',
                            text: errors.message,
                            type: 'error',
                            duration: '3500'
                        });
                        $.each(errors['errors'], function (key, value) {
                            console.log('key ' + key);
                            console.log('value ' + value);
                            Vue.notify({
                                group: 'notification-template',
                                title: 'Error',
                                text: value,
                                type: 'error',
                                duration: '3500'
                            });

                        });
                    }
                })

            });
    });
</script>
<style lang="scss">
    .gantt-wrapper {
        width: calc(100vw - 45px);
        background-color: #afafaf;
        position: relative;
        display: flex;
        /*overflow-y: auto;*/
        &.fixed {
            .list {
                position: fixed;
                top: 38px;
                z-index: 2;
            }
            .segments {
                margin-left: 390px;
            }
        }
        .list {
            background-color: #eeeeee;
            position: relative;
            left: 0;
            top: 0;
            li {
                padding: 5px 10px;
                max-height: 30px;
                border-bottom: 1px solid black;
            }
            #v-tasks-list {
                width: max-content;
            }
        }
        .segments {
            background-color: #fff;
            /*min-width: 80%;*/
            height: 100%;
            position: relative;
            top: 0;
            right: 0;
            li {
                border-bottom: 1px solid black;
                .task {
                    background-color: #C1EAFF;
                    color: transparent;
                    border-radius: 5px;
                    max-height: 25px;
                    padding: 5px 0;
                    margin: 2px 0;
                    display: block;
                    /*cursor: pointer;*/
                    position: relative;
                    &.hovered {
                        background-color: red;
                    }
                }
                .gripper-left, .gripper-right {
                    width: 3px;
                    cursor: ew-resize;
                }
            }
            #days-list, #week-list {
                list-style: none;
                display: flex;
                padding: 0;
                margin: 0;
                li {
                    min-width: 45px;
                    border-right: 0.1px solid black;
                    height: 30px;
                }
            }
        }
        .tooltip-wrapper {
            display: none;
            background-color: #fbffb8;
            color: #000;
            position: absolute;
            padding: 10px;
            border-radius: 10px;
            width: 68px;
            &:after {
                content: "";
                position: absolute;
                right: 100%;
                top: 35px;
                left: 20px;
                border-top: 13px solid #fbffb8;
                border-right: 13px solid transparent;
                border-bottom: 13px solid transparent;
                border-left: 13px solid transparent;
            }
            span {
                margin: 0 5px;
            }
        }
    }

    #week-list-clone, #days-list-clone {
        list-style: none;
        display: flex;
        padding: 0;
        margin: 0;
        top: 0;
        background: white;
        z-index: 1;
        li {
            min-width: 45px;
            border-right: 0.1px solid black;
            border-bottom: 1px solid black;
            height: 30px;
        }
    }

    #days-list-clone {
        top: 30px;
    }
</style>
