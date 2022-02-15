package logic

import (
	"backend/common/errorx"
	"backend/service/k8s/cmd/api/internal/svc"
	"backend/service/k8s/cmd/api/internal/types"
	"context"
	"github.com/zeromicro/go-zero/core/logx"
	metav1 "k8s.io/apimachinery/pkg/apis/meta/v1"
	"k8s.io/client-go/kubernetes"
	"k8s.io/client-go/tools/clientcmd"
	"log"
)

type UpdateServiceLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewUpdateServiceLogic(ctx context.Context, svcCtx *svc.ServiceContext) UpdateServiceLogic {
	return UpdateServiceLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *UpdateServiceLogic) UpdateService(req types.RequestCreateService) (*types.Response, error) {
	configPath := "k8s/kube.config"

	config, err := clientcmd.BuildConfigFromFlags("", configPath)
	if err != nil {
		log.Fatal(err)
	}
	clientSet, err := kubernetes.NewForConfig(config)
	if err != nil {
		log.Fatal(err)
	}
	getService, err := clientSet.CoreV1().Services(req.Namespace).Get(context.TODO(), req.Name, metav1.GetOptions{})

	getService.Labels = getLabelsMap(req.Labels)
	getService.Spec.Selector = getSelector(req.Labels, req.AppName)
	getService.Spec.Ports = getServicePorts(req.Ports)

	update, err := clientSet.CoreV1().Services(req.Namespace).Update(context.TODO(), getService, metav1.UpdateOptions{})
	if err != nil {
		return nil, errorx.NewCodeError(201, "更新失败", err)
	}
	return nil, errorx.NewCodeError(200, "成功", update)
}
